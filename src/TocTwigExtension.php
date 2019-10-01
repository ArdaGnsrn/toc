<?php

/**
 * PHP TableOfContents Library
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/caseyamcl/toc
 * @version 2
 * @package caseyamcl/toc
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * ------------------------------------------------------------------
 */

declare(strict_types=1);

namespace TOC;

use Twig_Extension;
use Twig_SimpleFilter;
use Twig_SimpleFunction;

/**
 * Table of Contents Twig Extension Integrates with Twig
 *
 * Adds filter:
 * - add_anchors
 *
 * Adds functions:
 * - toc (returns HTML list)
 * - toc_items (returns KnpMenu iterator)
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class TocTwigExtension extends Twig_Extension
{
    /**
     * @var TocGenerator
     */
    private $generator;

    /**
     * @var MarkupFixer
     */
    private $fixer;



    /**
     * Constructor
     *
     * @param TocGenerator $generator
     * @param MarkupFixer $fixer
     */
    public function __construct(TocGenerator $generator = null, MarkupFixer $fixer = null)
    {
        $this->generator = $generator ?: new TocGenerator();
        $this->fixer     = $fixer     ?: new MarkupFixer();
    }



    /**
     * @return array|Twig_SimpleFilter[]
     */
    public function getFilters(): array
    {
        $filters = parent::getFilters();

        $filters[] = new Twig_SimpleFilter('add_anchors', function ($str, $top = 1, $depth = 6) {
            return $this->fixer->fix($str, $top, $depth);
        }, ['is_safe' => ['html']]);

        return $filters;
    }



    /**
     * @return array|Twig_SimpleFunction[]
     */
    public function getFunctions(): array
    {
        $functions = parent::getFunctions();

        // ~~~

        $functions[] = new Twig_SimpleFunction('toc', function ($markup, $top = 1, $depth = 6) {
            return $this->generator->getHtmlMenu($markup, $top, $depth);
        }, ['is_safe' => ['html']]);

        // ~~~

        $functions[] = new Twig_SimpleFunction('toc_items', function ($markup, $top = 1, $depth = 6) {
            return $this->generator->getMenu($markup, $top, $depth);
        });

        $functions[] = new Twig_SimpleFunction('add_anchors', function ($markup, $top = 1, $depth = 6) {
            return $this->fixer->fix($markup, $top, $depth);
        }, ['is_safe' => ['html']]);

        return $functions;
    }



    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName(): string
    {
        return 'toc';
    }
}
