<?php
/**
 * Class Debug
 * Simple debug class
 *
 * @uses \Bitrix\Main\Page\Asset, \Bitrix\Main\Page\AssetLocation, \CJSCore, \CEventLog
 *
 * @author    Roman Shershnev <readytoban@gmail.com>
 * @version   2.1
 * @package   CodeCraft
 * @category  Debug
 * @copyright Copyright © 2014-2016, Roman Shershnev
 */

namespace DoctorNet;

use Bitrix\Main\Page\Asset,
    Bitrix\Main\Page\AssetLocation;

class Debug {
    
    static protected $instance;
    
    protected $validIPs   = [
        '91.205.44.154',  // codecraft external IP
        '217.25.228.194', // doctornet external IP

        '192.168.1.196',  // manriel work PC on local network
    ];
    protected $validHosts = [
        'manriel.name',   // manriel's home host
    ];

    protected $colorPaletteVGA   = [ // standard VGA 16 colors palette
        '#000000', // black
        '#000080', // navy
        '#008000', // green
        '#008080', // teal
        '#800000', // maroon
        '#800080', // purple
        '#808000', // olive
        '#C0C0C0', // silver
        '#808080', // gray
        '#0000FF', // blue
        '#00FF00', // lime
        '#00FFFF', // aqua
        '#FF0000', // red
        '#FF00FF', // fuchsia
        '#FFFF00', // yellow
        '#FFFFFF', // white
    ];
    protected $colorPaletteShell = [
        "\e[0m", // default
        "\e[40m\e[34m", // blue
        "\e[40m\e[32m", // green
        "\e[40m\e[36m", // cyan
        "\e[40m\e[31m", // red
        "\e[40m\e[35m", // magenta
        "\e[40m\e[33m", // yellow
        "\e[40m\e[37m", // white
        "\033[37m\033[47m", // black on white
        "\e[40m\e[34m", // blue
        "\e[40m\e[32m", // green
        "\e[40m\e[36m", // cyan
        "\e[40m\e[31m", // red
        "\e[40m\e[35m", // magenta
        "\e[40m\e[33m", // yellow
        "\e[40m\e[37m", // white
    ];
    
    protected $var;
    protected $message;
    protected $htmlColor;
    protected $cliColor;
    protected $isPlainOut;
    
    protected $depthCounter;
    protected $maxDepth;
    protected $expandLevel;

    /**
     * @param int $expandLevel
     *
     * @return $this;
     */
    public function setExpandLevel($expandLevel = 2) {
        $expandLevel       = intval($expandLevel) > 0 ? intval($expandLevel) : 2;
        $this->expandLevel = $expandLevel;

        return $this;
    }

    private $recursiveStack = [];
    
    /**
     * Debug constructor.
     *
     * @param mixed $var
     */
    public function __construct($var = null) {
        $style  = <<<STYLE
<style>
    .codecraft-debug-more {
        cursor: pointer;
        user-select: none;
        -moz-user-select: none;
        -khtml-user-select: none;
        -webkit-user-select: none;
        -o-user-select: none;
        opacity: 0.4;
    }
</style>
STYLE;
        $script = <<<SCRIPT
<script>
    jQuery(function($){
        $('.codecraft-debug-more').on('click', function(event){
            if ($(this).text() == '...') {
                $(this).html('&nbsp;свернуть&nbsp;');
                $(this).next('.codecraft-debug-array').show().next('.codecraft-debug-pad').show();
            } else {
                $(this).text('...');
                $(this).next('.codecraft-debug-array').hide().next('.codecraft-debug-pad').hide();
            }
        });
    })
</script>
SCRIPT;
        \CJSCore::Init(['jquery']);

        $asset = Asset::getInstance();
        $asset->addString($style, true, AssetLocation::AFTER_CSS);
        $asset->addString($script, true, AssetLocation::AFTER_JS);
        
        $this->setVar($var)->setColor()->setMessage()->setExpandLevel()->setMaxDepth();
    }
    
    /**
     * @param mixed      $var
     * @param bool|false $die
     * @param array      $params default is array('color' => 2, 'message' => '', 'expand' => 2, 'maxDepth' => '20');
     *
     * @return $this|bool
     */
    public function __invoke() {
        
        $args = func_get_args();
        if (count($args) <= 0) {
            return false;
        }
        
        $var    = $args[0];
        $isDie  = $args[1];
        $params = $args[2];
        
        $message = $color = $expandLevel =  $maxRecursiveDepth = null;
        
        if (!is_bool($isDie) && is_array($isDie)) {
            $params = $isDie;
            $isDie  = false;
        }
        if (!is_bool($isDie)) {
            $message = $isDie;
            $isDie   = false;
            if (is_string($message) && preg_match("/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/", $message)) {
                $color   = $message;
                $message = null;
            }
        }
        if (is_array($params)) {
            $color             = $params['color'] ?: null;
            $message           = $params['message'] ?: null;
            $expandLevel       = $params['expand'] ?: null;
            $maxRecursiveDepth = $params['maxDepth'] ?: null;
        }
        
        $instance = new Debug();
        $instance->setVar($var)->setMessage($message)->setColor($color)->setExpandLevel($expandLevel);
        if (is_int($maxRecursiveDepth) && $maxRecursiveDepth > 0) {
            $instance->setMaxDepth($maxRecursiveDepth);
        }
        $instance->out();
        if ($isDie) {
            $this->terminate();
        }
        
        return $this;
    }
    
    /**
     * @param int|string $color
     *
     * @return $this
     */
    public function setColor($color = 2) {
        if (intval($color)) {
            $this->htmlColor = $this->colorPaletteVGA[$color % 16];
            $this->cliColor  = $this->colorPaletteShell[$color % 16];
        } else {
            $this->htmlColor = $color;
        }
        
        return $this;
    }
    
    /**
     * @param string $message
     *
     * @return $this
     */
    public function setMessage($message = '') {
        $this->message = $message;
        
        return $this;
    }
    
    /**
     * @param $var
     *
     * @return $this
     */
    public function setVar($var) {
        $this->var = $var;
        
        return $this;
    }
    
    /**
     * @param int $depth
     *
     * @return $this
     */
    public function setMaxDepth($depth = 20) {
        $this->maxDepth = $depth;
        
        return $this;
    }
    
    /**
     * @param bool $isPlainTextOut
     *
     * @return $this
     */
    public function setPlainTextOut($isPlainTextOut = false) {
        $this->isPlainOut = (bool)$isPlainTextOut;
        
        return $this;
    }
    
    /**
     * @return $this
     */
    public function out() {
        if ($this->isCliCall()) {
            $this->outCli();
        } elseif ($this->hasPermissions()) {
            $this->outHtml();
        }
        
        return $this;
    }
    
    /**
     * @return $this
     */
    protected function outCli() {
        echo $this->cliColor;
        $this->outPlainText();
        echo $this->colorPaletteShell[0];
        
        return $this;
    }
    
    /**
     * @return $this
     */
    protected function outHtml() {
        $str = <<<HEREDOC
<table border="1" style="font-size: 8pt !important; line-height: 9pt !important; border: 1px solid {$this->htmlColor}; border-collapse: collapse; color: {$this->htmlColor} !important;">
    <thead>
        <tr>
            <th>{$this->debugFileInfo()}</th>
        </tr>
        <tr>
            <th>{$this->message}</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{$this->export($this->var)}</td>
        </tr>
    </tbody>
</table>
HEREDOC;
        echo $str;

        return $this;
    }
    
    /**
     * @return $this
     */
    protected function outPlainText() {
        echo '-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-' . PHP_EOL
             . $this->debugFileInfo() . PHP_EOL . $this->message . PHP_EOL
             . '-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-' . PHP_EOL;
        var_export($this->var);
        echo PHP_EOL . '-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-' . PHP_EOL;
        
        return $this;
    }
    
    /**
     * @param $var
     *
     * @return string
     */
    public function export($var) {
        return "<pre style='font-size: 8pt !important; line-height: 9pt !important; color: {$this->htmlColor} !important;'>{$this->export_recursive($var)}</pre>";
    }
    
    /**
     * @param $var
     *
     * @return string
     */
    protected function export_recursive($var) {
        $this->depthCounter++;
        $isExplanded = $this->depthCounter <= $this->expandLevel;
        $result = '';
        $pad    = str_pad('', 6 * 4 * $this->depthCounter, '&nbsp;', STR_PAD_LEFT);
        $isObj  = false;

        $recursiveCheck = false;
        if (is_object($var) || (is_array($var) && !empty($var))) {
            if (in_array($var, $this->recursiveStack, true)) {
                $result .= '[RECURSION]';
                $this->depthCounter--;

                return $result;
            } else {
                array_push($this->recursiveStack, $var);
                $recursiveCheck = true;
            }
        }
        if (is_object($var)) {
            if ($this->depthCounter > $this->maxDepth) {
                $result .= $pad . '[MAX DEPTH REACHED!]';
                $this->depthCounter--;

                return $result;
            }
            $isObj = true;
            $result .= '<span>' . get_class($var) . '::__set_state(</span>';
            $var = get_object_vars($var);
        }
        if (is_array($var) && !empty($var)) {
            $result .= '<span>array(</span>';
            $result .= ($isExplanded) ? '<span class="codecraft-debug-more">&nbsp;свернуть&nbsp;</span>'
                : '<span class="codecraft-debug-more">...</span>';
            $result .= '<div class="codecraft-debug-array"';
            $result .= ($isExplanded) ? '' : ' style="display: none"';
            $result .= '>';
            if ($this->depthCounter > $this->maxDepth) {
                $result .= '<div>' . $pad . '[MAX DEPTH REACHED!]</div>';
            } else {
                foreach ($var as $k => $v) {
                    $result .= '<div>';
                    $result .= $pad . htmlspecialchars(var_export($k, true)) . ' => ' . $this->export_recursive($v)
                               . ',';
                    $result .= '</div>';
                }
            }
            $result .= '</div>';
            $this->depthCounter--;
            $pad = str_pad('', 6 * 4 * $this->depthCounter, '&nbsp;', STR_PAD_LEFT);
            $result .= '<span class="codecraft-debug-pad"';
            $result .= ($isExplanded) ? '' : ' style="display: none"';
            $result .= '>' . $pad . '</span><span>)</span>';
            if ($isObj) {
                $result .= '<span>)</span>';
            }

        } elseif (is_array($var) && empty($var)) {
            $result .= 'array()';
            if ($isObj) {
                $result .= '<span>)</span>';
            }
            $this->depthCounter--;

        } else {
            $tmpResult = explode(PHP_EOL, htmlspecialchars(var_export($var, true)));
            $result .= array_shift($tmpResult);
            $lastElement = array_pop($tmpResult);
            if (count($tmpResult)) {
                $result .= '<span class="codecraft-debug-more">...</span>';
                $result .= '<div class="codecraft-debug-array" style="display: none">';
                $result .= implode(PHP_EOL, $tmpResult);
                $result .= '</div>';
            }
            if ($lastElement) {
                $result .= PHP_EOL.$lastElement;
            }
            $this->depthCounter--;

        }
        if ($recursiveCheck) {
            array_pop($this->recursiveStack);
        }

        return $result;
    }

    /**
     * @return $this
     */
    public function outToEventLog() {
        $oEventLog = new \CEventLog();
        $oEventLog->Add([
            "SEVERITY"      => "SECURITY",
            "AUDIT_TYPE_ID" => "DEBUG_MESSAGE",
            "MODULE_ID"     => "DEBUG",
            "ITEM_ID"       => $this->message,
            "DESCRIPTION"   => var_export($this->var, true),
        ]);

        return $this;
    }
    
    /**
     * @return null
     */
    public function terminate() {
        if ($this->hasPermissions()) {
            die();
        }
        
        return null;
    }
    
    /**
     * @return string
     */
    protected function debugFileInfo() {
        $debugBacktrace = debug_backtrace();

        return $debugBacktrace[4]["file"] . " (" . $debugBacktrace[4]["line"] . ") ";
    }
    
    /**
     * @return bool
     */
    protected function hasPermissions() {
        return $this->isValidIP($_SERVER['REMOTE_ADDR']) || $this->isSetCookie();
    }
    
    /**
     * @param $ip
     *
     * @return mixed
     */
    protected function isValidIP($ip) {
        $arrIPs = $this->validIPs;
        foreach ($this->validHosts as $host) {
            $hostIp = gethostbyname($host);
            if ($hostIp) {
                $arrIPs[] = $hostIp;
            }
        }
        
        return in_array($ip, $arrIPs);
    }
    
    /**
     * @return bool
     */
    protected function isSetCookie() {
        return isset($_COOKIE['DEBUG']);
    }
    
    /**
     * @return bool
     */
    protected function isCliCall() {
        return PHP_SAPI == 'cli';
    }
    
}