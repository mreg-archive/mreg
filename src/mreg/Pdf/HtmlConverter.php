<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 *
 * @package mreg\Pdf
 */
namespace mreg\Pdf;


// Det verkar som att jag kan göra allt som den här gör med Snappy istället!!
// se: https://github.com/KnpLabs/snappy

/*
    den här filen är helt utdaterad, den står kvar här bara
    för att påminna mig om att jag ska använda snappy istället!!
*/



/**
 * Generate PDF from HTML and CSS input using wkhtmltopdf.
 * Specify the location of the wkhtmltopdf binary be defining PATH_TO_WKHTMLTOPDF.
 *
 * <code>define('PATH_TO_WKHTMLTOPDF', '/usr/local/lib/wkhtmltopdf/wkhtmltopdf-i386');
 * $htmltopdf = new HtmlConverter("&lt;h1&gt;foo&lt;/h1&gt;");
 * $htmltopdf->loadCss('h1 {color:green;}');
 * $htmltopdf->setMargins(5);
 * $htmltopdf->setHeader('[section]', $align='center');
 * $htmltopdf->setLowres(true);
 * $htmltopdf->setOutline(true);
 * $pdf = $htmltopdf->get();
 * header("Content-Type: application/pdf");
 * header('Content-Disposition: attachment; filename="test.pdf";');
 * echo $pdf;</code>
 *
 * @link http://code.google.com/p/wkhtmltopdf/
 * @package mreg\Pdf
 */
class HtmlConverter {

    /**
     * Array of loaded html strings
     * @var array
     */
    private $arHtml = array();


    /**
     * Paper margins. Top, right, bottom, left.
     * @var array
     */
    private $margins = array();


    /**
     * Page headers. left, center and right.
     * @var array
     */
    private $headers = array();


    /**
     * Page footers. left, center and right.
     * @var array
     */
    private $footers = array();


    /**
     * Loaded css string
     * @var string
     */
    private $css = '';


    /**
     * Paper sice. A4, A3, Letter, etc.
     * @var string
     */
    private $paper = 'A4';


    /**
     * Paper oriantation. 'Landscape' or 'Portrait'
     * @var string
     */
    private $orientation = 'Portrait';


    /**
     * Encoding
     * @var string
     */
    private $encoding = 'utf8';


    /**
     * Render pdf in grayscale?
     * @var bool
     */
    private $grayscale = false;
    
    
    /**
     * Render pdf in low resolution?
     * @var bool
     */
    private $lowres = true;


    /**
     * Render pdf outline?
     * @var bool
     */
    private $outline = true;
    


    /* SETTERS */


    /**
     * Load a html string to be written to pdf. Multiple calls will
     * generate all the loaded content on separate pages in pdf.
     * @param string $html
     * @return void
     */
    public function loadHtml($html)
    {
        assert('is_string($html)');
        $this->arHtml[] = $html;
    }


    /**
     * Clear all loaded html strings
     * @return void
     */
    public function clearHtml()
    {
        $this->arHtml = array();
    }


    /**
     * Set css string for all loaded html strings
     * NOTE: mutliple calls will owerwrite previous values
     * @param string $css
     * @return void
     */
    public function loadCss($css)
    {
        assert('is_string($css)');
        $this->css = $css;
    }


    /**
     * Set paper margins
     * @param int $top
     * @param int $right
     * @param int $bottom
     * @param int $left
     * @return void
     */
    public function setMargins($top, $right=null, $bottom=null, $left=null)
    {
        assert('is_int($top)');
        assert('is_int($right) || is_null($right)');
        assert('is_int($bottom) || is_null($bottom)');
        assert('is_int($left) || is_null($left)');
        $this->margins = array($top, $top, $top, $top);
        if ( is_int($right) ) {
            $this->margins[1] = $right;
            $this->margins[3] = $right;
        }
        if ( is_int($bottom) ) $this->margins[2] = $bottom;
        if ( is_int($left) ) $this->margins[3] = $left;
    }


    /**
     * Set a page header. Three different headers can be specified 'left',
     * 'right' and 'center'. The following values will be substituted:
     *
     * - [page]       Replaced by the number of the pages currently being printed
     * - [frompage]   Replaced by the number of the first page to be printed
     * - [topage]     Replaced by the number of the last page to be printed
     * - [webpage]    Replaced by the URL of the page being printed
     * - [section]    Replaced by the name of the current section
     * - [subsection] Replaced by the name of the current subsection
     *
     * @throws \InvalidArgumentException if header contians invalid characters
     * @param string $txt
     * @param string $align
     * @return void
     */
    public function setHeader($txt, $align='center')
    {
        assert('is_string($txt)');
        assert('$align == "left" || $align == "center" || $align == "right"');
        if ( !preg_match('#^[a-zA-ZåäöÅÄÖ0-9\[\]().:,;/ -]+$#', $txt) ) {
            throw new \InvalidArgumentException('Invalid page header. Only alpha numeric charactes and -.:,;/[]() are alowed');
        }
        $this->headers[$align] = $txt;
    }


    /**
     * Set a page footer. Three different fotters can be specified, 'left',
     * 'right' and 'center'.The following values will be substituted:
     *
     * - [page]       Replaced by the number of the pages currently being printed
     * - [frompage]   Replaced by the number of the first page to be printed
     * - [topage]     Replaced by the number of the last page to be printed
     * - [webpage]    Replaced by the URL of the page being printed
     * - [section]    Replaced by the name of the current section
     * - [subsection] Replaced by the name of the current subsection
     *
     * @throws \InvalidArgumentException if header contians invalid characters
     * @param string $txt
     * @param string $align
     * @return void
     */
    public function setFooter($txt, $align='center')
    {
        assert('is_string($txt)');
        assert('$align == "left" || $align == "center" || $align == "right"');
        if ( !preg_match('#^[a-zA-ZåäöÅÄÖ0-9\[\]().:,;/ -]+$#', $txt) ) {
            throw new \InvalidArgumentException('Invalid page footer. Only alpha numeric charactes and -.:,;/[]() are alowed');
        }
        $this->footers[$align] = $txt;
    }


    /**
     * Set paper sice. A4, A3, Letter, etc.
     * @param string $paper
     * @return void
     */
    public function setPaper($paper)
    {
        assert('is_string($paper)');
        $this->paper = escapeshellarg($paper);
    }


    /**
     * Set paper orientation. 'Landscape' or 'Portrait'
     * @param string $orientation
     * @return void
     */
    public function setOrientation($orientation)
    {
        assert('is_string($orientation)');
        assert('$orientation == "Landscape" || $orientation == "Portrait"');
        $this->orientation = escapeshellarg($orientation);
    }


    /**
     * Set encoding. Defaults to utf8.
     * @param string $encoding
     * @return void
     */
    public function setEncoding($encoding)
    {
        assert('is_string($encoding)');
        $this->encoding = escapeshellarg($encoding);
    }


    /**
     * Set if pdf should be rendered in grayscale
     * @param bool $flag
     * @return void
     */
    public function setGrayscale($flag)
    {
        assert('is_bool($flag)');
        $this->grayscale = $flag;
    }


    /**
     * Set if pdf should be rendered in low resolution
     * @param bool $flag
     * @return void
     */
    public function setLowres($flag)
    {
        assert('is_bool($flag)');
        $this->lowres = $flag;
    }


    /**
     * Set if pdf outlines (bookmarks) should be created
     * @param bool $flag
     * @return void
     */
    public function setOutline($flag)
    {
        assert('is_bool($flag)');
        $this->outline = $flag;
    }



    /* CONSTRUCTORS */


    /**
     * Create and optinaly load html
     * @throws \RuntimeException if PATH_TO_WKHTMLTOPDF is not defined
     * @param string $html
     */
    public function __construct($html='')
    {
        if ( !defined('PATH_TO_WKHTMLTOPDF') ) {
            throw new \RuntimeException('Define PATH_TO_WKHTMLTOPDF to locate your wkhtmltopdf binary.');
        }
        if ( !empty($html) ) $this->loadHtml($html);
    }


    /**
     * Generate and fetch pdf
     * @throws \RuntimeException if wkhtmltopdf returns an error code
     * @return \string
     */
    public function get()
    {
        //write html to disk
        $htmlFiles = array();
        foreach ( $this->arHtml as $html ) {
            $tmpName = self::genTmpName('html');
            file_put_contents($tmpName, $html);
            $htmlFiles[] = $tmpName;
        }

        //write css to disk
        $cssFile = '';
        if ( !empty($this->css) ) {
            $tmpName = self::genTmpName('css');
            file_put_contents($tmpName, $this->css);
            $cssFile = $tmpName;
        }

        $target = self::genTmpName('pdf');

        $cmd = $this->buildCommand($target, $htmlFiles, $cssFile);

        if ( ( $return = self::exec($cmd) ) !== 0 ) {
            throw new \RuntimeException("wkhtmltopdf returned error condition '$return' for command '$cmd'");
        }

        //clear tmp files
        foreach ( $htmlFiles as $fname ) {
            unlink($fname);
        }
        if ( !empty($cssFile) ) unlink($cssFile);

        //get pdf
        $pdf = file_get_contents($target);
        unlink($target);

        return $pdf;
    }



    /* HELPERS */

    
    /**
     * Build command from values set
     * @param string $target Filename
     * @param array $htmlFiles
     * @param string $cssFile
     * @return string
     */
    private function buildCommand($target, array $htmlFiles, $cssFile='')
    {
        assert('is_string($target)');
        assert('!empty($htmlFiles) /* loadHtml() before rendering */');

        $htmls = '';
        foreach ( $htmlFiles as $fname ) {
            $htmls .= "$fname ";
        }
        
        $cmd = PATH_TO_WKHTMLTOPDF." -q --load-error-handling ignore -s {$this->paper} -O {$this->orientation} --encoding {$this->encoding}";

        if ( $this->grayscale ) $cmd .= ' -g ';
        if ( $this->lowres ) $cmd .= ' -l ';
        if ( $this->outline ) $cmd .= ' --outline ';
        if ( !empty($cssFile) ) $cmd .= " --user-style-sheet $cssFile ";
        if ( !empty($this->margins) ) {
            $cmd .= " -T {$this->margins[0]} -R {$this->margins[1]} -B {$this->margins[2]} -L {$this->margins[3]}";
        }
        if ( isset($this->headers['left']) ) $cmd .= " --header-left \"{$this->headers['left']}\"  ";
        if ( isset($this->headers['center']) ) $cmd .= " --header-center \"{$this->headers['center']}\"  ";
        if ( isset($this->headers['right']) ) $cmd .= " --header-right \"{$this->headers['right']}\"  ";

        if ( isset($this->footers['left']) ) $cmd .= " --footer-left \"{$this->footers['left']}\"  ";
        if ( isset($this->footers['center']) ) $cmd .= " --footer-center \"{$this->footers['center']}\"  ";
        if ( isset($this->footers['right']) ) $cmd .= " --footer-right \"{$this->footers['right']}\"  ";

        $cmd .= " $htmls $target";

        return $cmd;
    }


    /**
     * Create temporary file with postfix
     * @param string $postfix
     * @return string Filename 
     */
    static private function genTmpName($postfix)
    {
        $tmpDir = sys_get_temp_dir();
        $tmpName = tempnam($tmpDir, "phpdf_");
        $postfixName = "$tmpName.$postfix";
        rename($tmpName, $postfixName);
        return $postfixName;
    }


    /**
     * Excecute command and get return code
     * @param string $cmd
     * @return int
     */
    static private function exec($cmd)
    {
        assert('is_string($cmd)');
        exec($cmd, $output, $return);
        return $return;
    }

}
