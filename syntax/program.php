<?php

class syntax_plugin_redfoftplhelper_program extends DokuWiki_Syntax_Plugin {

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
        return 280;
    }

    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern('{{fofprogram\|.+?>.+?}}', $mode, 'plugin_redfoftplhelper_program');
    }

    public function handle($match, $state, $pos, Doku_Handler $handler) {
        preg_match('/{{fofprogram\|(.+?)>(.+?)}}/', $match, $programData);
        list(, $lang, $datapage) = $programData;
        $data = @json_decode(rawWiki($datapage));
        return [$lang, $data];
    }

    public function render($mode, Doku_Renderer $renderer, $data) {
        global $conf;

        setlocale(LC_TIME, $this->getLocale($conf['lang']));

        list($lang, $program) = $data;
        if ($mode == 'xhtml') {
            $lastDay = null;
            $renderer->doc .= '<div class="program">';
            foreach ($program->data as $event) {
                if ($lastDay !== $this->getDay($event->date->start)) {
                    $lastDay = $this->getDay($event->date->start);

                    // Print new Day
                    $renderer->doc .= '<h2>' . $lastDay[0] . ' <small>' . $lastDay[1] . '</small></h2>';
                }

                $renderer->doc .= '<div class="row event event-' . $event->type . '"><div class="col-md-2 time"> ' . $this->getHour($event->date->start) . ' - ' . $this->getHour($event->date->end) . '</div>';

                switch ($event->type) {
                    case 'info':
                        $renderer->doc .= '<div class="col-md parallel">';
                        $renderer->doc .= '<div class="name">' . $event->descriptions->{$lang}->name . '</div>';
                        if ($event->descriptions->{$lang}->description) $renderer->doc .= '<div class="description">' . $event->descriptions->{$lang}->description . '</div>';
                        $renderer->doc .=  '</div>';
                        break;
                    case 'chooser':
                        foreach ($event->parallels as $parallel) {
                            $renderer->doc .= '<div class="col-md parallel">';
                            $renderer->doc .= '<div class="name">' . $parallel->{$lang}->name . '</div>';
                            if ($parallel->{$lang}->description) $renderer->doc .= '<div class="description">' . $parallel->{$lang}->description . '</div>';
                            if ($parallel->{$lang}->place) $renderer->doc .= '<div class="place">' . $parallel->{$lang}->place . '</div>';

                            if ($parallel->price->kc) $renderer->doc .= '<div class="price">' . $parallel->price->kc . ' CZK/' . $parallel->price->eur . ' EUR</div>';
                            $renderer->doc .=  '</div>';
                        }
                        break;
                }

                $renderer->doc .= '</div>';
            }
            $renderer->doc .= '</div>';
        }
        return false;
    }

    private function getDay($date) {
        $ts = $this->getTimestamp($date);
        return [strftime('%A', $ts), strftime('%e. %m.', $ts)];
    }

    private function getHour($date) {
        return strftime('%H:%M', $this->getTimestamp($date));
    }

    private function getTimestamp($date) {
        return DateTime::createFromFormat('Y-m-d H: i: s', $date)->getTimestamp();
    }

    private function getLocale($lang) {
        $entries = explode(',', $this->getConf('locales'));
        foreach ($entries as $entry) {
            list ($entry_lang, $entry_locale) = explode(':', $entry);
            if ($lang === $entry_lang) {
                return $entry_locale;
            }
        }

        return false;
    }
}
