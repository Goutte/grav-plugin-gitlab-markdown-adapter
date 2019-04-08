<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use RocketTheme\Toolbox\Event\Event;

/**
 * Adapts Gitlab's markdown into a digestible format for Grav and consorts.
 *
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
        // Don't proceed if we are in the admin plugin. Why? No idea.
        if ($this->isAdmin()) {
            return;
        }

        // Enable the main event we are interested in.
        // We set a high priority since we want to get there before the other plugins.
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

        //file_put_contents('TEST.LOG', $content);

        // Finally, set the new raw content.
        $e['page']->setRawContent($content);
    }

    /**
     * Replaces the opening and closing tags of a block by the provided replacements.
     * The whole matches are replaced. Use lookaheads and lookbehinds if necessary.
     *
     * This is a bit more verbose than preg_replace, but perhaps it's more resilient.
     *
     * @param string $content
     * @param string $opening_regex
     * @param string $closing_regex
     * @param string $new_opening
     * @param string $new_closing
     * @return string
     */
    protected function replaceBlockIn($content, $opening_regex, $closing_regex, $new_opening, $new_closing)
    {
        $cursor = 0;
        $got_more_openings = true;

        while ($got_more_openings) { // … we accept resumes
            $opening_match = array();
            preg_match($opening_regex, $content, $opening_match, PREG_OFFSET_CAPTURE, $cursor);

            if (empty($opening_match)) {
                $got_more_openings = false;
                // $cursor = <end> ? ideally…
                continue;
            }

            $om = $opening_match[0];

            if ($om[1] < $cursor) {
                // Yikes! A previous block ended before this one started!
                break;
            }

            $cursor = $om[1];

            $closing_match = array();
            preg_match($closing_regex, $content,
                $closing_match,PREG_OFFSET_CAPTURE,
                $cursor + strlen($om[0])
            );

            if (empty($closing_match)) {
                // Yikes! Nothing up to the end of the file?
                break;
            }

            $cm = $closing_match[0];

            // /!. substr_replace may possibly choke on multibyte strings.

            // Replace closing first because replacing opening shifts the positions.
            $content = substr_replace($content, '', $cm[1], strlen($cm[0]));
            $content = substr_replace($content, $new_closing, $cm[1], 0);

            $content = substr_replace($content, '', $om[1], strlen($om[0]));
            $content = substr_replace($content, $new_opening, $om[1], 0);

            $cursor = $cm[1] + strlen($new_closing) + (strlen($new_opening) - strlen($om[0]));
        }

        return $content;
    }
}
