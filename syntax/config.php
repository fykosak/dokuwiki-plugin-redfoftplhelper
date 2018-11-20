<?php
/**
 * DokuWiki Plugin redfoftplhelper (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Štěpán Stenchlák <stenchlak@fykos.cz>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) {
    die();
}

class syntax_plugin_redfoftplhelper_config extends DokuWiki_Syntax_Plugin
{
    private $data = [];

    /**
     * @return string Syntax mode type
     */
    public function getType()
    {
        return 'substition';
    }

    /**
     * @return string Paragraph type
     */
    public function getPType()
    {
        return 'block';
    }

    /**
     * @return int Sort order - Low numbers go before high numbers
     */
    public function getSort()
    {
        return 125;
    }

    /**
     * Connect lookup pattern to lexer.
     *
     * @param string $mode Parser mode
     */
    public function connectTo($mode)
    {
        $this->Lexer->addSpecialPattern('~~tpl:.+?\|?.+?~~', $mode, 'plugin_redfoftplhelper_config');
        $this->updateMetadata();
    }

    /**
     * Handle matches of the redfoftplhelper syntax
     *
     * @param string       $match   The match of the syntax
     * @param int          $state   The state of the handler
     * @param int          $pos     The position in the document
     * @param Doku_Handler $handler The handler
     *
     * @return array Data for the renderer
     */
    public function handle($match, $state, $pos, Doku_Handler $handler)
    {
        preg_match('/~~tpl:(.+?)(\|(.+?))?~~/', $match,$matches);

        if (count($matches) === 4) {
            return [$matches[1] => $matches[3]];
        } else {
            return [$matches[1] => true];
        }
    }

    /**
     * Render xhtml output or metadata
     *
     * @param string        $mode     Renderer mode (supported modes: xhtml)
     * @param Doku_Renderer $renderer The renderer
     * @param array         $data     The data from the handler() function
     *
     * @return bool If rendering was successful.
     */
    public function render($mode, Doku_Renderer $renderer, $data)
    {
        $this->data = array_merge($this->data, $data);
        $this->updateMetadata();
        return true;
    }

    private function updateMetadata() {
        global $ID;
        p_set_metadata($ID, ['redfoftpl' => $this->data]);
    }
}

