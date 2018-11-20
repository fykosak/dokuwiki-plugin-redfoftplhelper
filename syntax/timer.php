<?php

class syntax_plugin_redfoftplhelper_timer extends DokuWiki_Syntax_Plugin {

    public function getType() {
        return 'substition';
    }

    public function getPType() {
        return 'normal';
    }

    public function getAllowedTypes() {
        return [];
    }

    public function getSort() {
        return 225;
    }

    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern('{{tplfoftimer>.+?}}', $mode, 'plugin_redfoftplhelper_timer');
    }

    public function handle($match, $state, $pos, Doku_Handler $handler) {

        switch ($state) {
            case DOKU_LEXER_SPECIAL:
                $match = substr($match, 14, -2);
                return [$state, ['date' => $match]];
            default:
                return [$state, []];
        }
    }

    public function render($mode, Doku_Renderer $renderer, $data) {

        if ($mode == 'xhtml') {
            list($state, $params) = $data;
            switch ($state) {
                case DOKU_LEXER_SPECIAL:
                    $renderer->doc .= '<div class="tpl-countdown row" data-date="' . $params['date'] . '">';
                    $renderer->doc .= '</div>';
                    return true;
                default:
                    return true;
            }
        }
        return false;
    }
}
