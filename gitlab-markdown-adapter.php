<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use RocketTheme\Toolbox\Event\Event;

/**
 * Class GitlabMarkdownAdapterPlugin
 * @package Grav\Plugin
 */
class GitlabMarkdownAdapterPlugin extends Plugin
{
    /**
     * @return array
     *
     * The getSubscribedEvents() gives the core a list of events
     *     that the plugin wants to listen to. The key of each
     *     array section is the event that the plugin listens to
     *     and the value (in the form of an array) contains the
     *     callable (or function) as well as the priority. The
     *     higher the number the higher the priority.
     */
    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0]
        ];
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized()
    {
        // Don't proceed if we are in the admin plugin.
        if ($this->isAdmin()) {
            return;
        }

        // Enable the main event we are interested in.
        // We set a high priority since we want to get there before other plugins.
        $this->enable([
            'onPageContentRaw' => ['onPageContentRaw', 10]
        ]);
    }

    /**
     * Do some work for this event, full details of events can be found
     * on the learn site: http://learn.getgrav.org/plugins/event-hooks
     *
     * @param Event $e
     */
    public function onPageContentRaw(Event $e)
    {
        // Get a variable from the plugin configuration
        //$text = $this->grav['config']->get('plugins.gitlab-markdown-adapter.text_var');

        // Get the current raw content.
        $content = $e['page']->getRawContent();

        // Convert `mermaid` code blocks to `[mermaid][/mermaid]`.
        // (?<!.) is a negative lookbehind to ensure we're at the beginning of a line.
        $content = $this->replaceBlockIn($content,
            '/(?<!.)``` *mermaid/i',
            '/(?<!.)```/',
            '[mermaid]',
            '[/mermaid]'
        );

        // Convert `math` code blocks to `$$ … $$`.
        // (?<!.) is a negative lookbehind to ensure we're at the beginning of a line.
        $content = $this->replaceBlockIn($content,
            '/(?<!.)``` *maths?/i',
            '/(?<!.)```/',
            '$$', '$$'
        );

        // Convert $`…`$ to $…$
        $content = $this->replaceBlockIn($content,
            '/[$][`]/',
            '/[`][$]/',
            '$', '$'
        );

        file_put_contents('TEST.LOG', $content);

        // Finally, set the new raw content.
        $e['page']->setRawContent($content);
    }

    protected function replaceBlockIn($content, $opening_regex, $closing_regex, $new_opening, $new_closing)
    {
//        $opening_matches = array();
//        preg_match_all($opening_regex, $content, $opening_matches, PREG_OFFSET_CAPTURE);

//        if (empty($opening_matches)) return $content;
//        if (empty($opening_matches[0])) return $content;

        $cursor = 0;
        $got_more_openings = true;

        while ($got_more_openings) { // … we accept resumes
            $opening_match = array();
            preg_match($opening_regex, $content, $opening_match, PREG_OFFSET_CAPTURE, $cursor);

            if (empty($opening_match)) {
                $got_more_openings = false;
                // $cursor = <end> ?
                continue;
            }

            $om = $opening_match[0];

            if ($om[1] < $cursor) {
                // Yikes! A previous block ended before this one started!
                continue;
            }

            $cursor = $om[1];

            $closing_match = array();
            preg_match($closing_regex, $content,
                $closing_match,PREG_OFFSET_CAPTURE,
                $cursor + strlen($om[0])
            );

            if (empty($closing_match)) {
                // Yikes! Nothing up to the end of the file ?
                continue;
            }

            // /!. substr_replace may possibly choke on multibyte strings.

            // Replace closing first because replacing opening shifts the positions.
            $content = substr_replace($content, '', $closing_match[0][1], strlen($closing_match[0][0]));
            $content = substr_replace($content, $new_closing, $closing_match[0][1], 0);

            $content = substr_replace($content, '', $om[1], strlen($om[0]));
            $content = substr_replace($content, $new_opening, $om[1], 0);

            $cursor = $closing_match[0][1] + strlen($new_closing) + (strlen($new_opening) - strlen($om[0]));
        }

        return $content;
    }
}
