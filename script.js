/**
 * Timer
 **/

jQuery(() => {
    "use strict";
    const step = 1000;

    const addTimeElement = (time, text) => {
        return '<div class="col"><div class="time">' + time + '</div><div class="note">' + text + "</div></div>";
    };

    const pad = (number, size) => {
        let n = String(number);
        while (n.length < (size || 2)) {n = "0" + n;}
        return n;
    };

    const switchLang = (number, index) => {
        let key;
        switch (number) {
            case 1:
                key = 'SgN';
                break;
            case 2:
            case 3:
            case 4:
                key = 'PlN';
                break;
            case 0:
            default:
                key = 'PlG';
                break;
        }
        return LANG.plugins.fkstimer[index + key];
    };

    const getTimeElements = (delta) => {
        if (delta < 0) {
            return LANG.plugins.fkstimer['past-event'];
        }
        delta -= (60 * 60 * 1000);
        const time = (new Date(delta));
        const hours = time.getHours();
        const days = time.getDate() + (time.getMonth() * 31) - 1;

        let html = '';
        if (days) {
            html += addTimeElement(days, switchLang(days, 'day'));
        }

        html += addTimeElement(pad(hours, 2), switchLang(hours, 'hour'));

        const min = time.getMinutes();
        html += addTimeElement(pad(min, 2), switchLang(min, 'min'));

        const sec = time.getSeconds();
        html += addTimeElement(pad(sec, 2), switchLang(sec, 'sec'));

        return html;
    };
    /**
     *
     * @param span Element
     * @param deltaServer number
     */
    const countDown = (span, deltaServer) => {
        const current = (new Date()).getTime() + deltaServer;
        const deadline = (new Date(span.getAttribute('data-date'))).getTime();
        const delta = deadline - current;
        span.innerHTML = getTimeElements(delta, deltaServer);
        setTimeout(() => {
            countDown(span, deltaServer);
        }, step);
    };
    document.querySelectorAll('.tpl-countdown').forEach((element) => {
        const metaTag = document.querySelector('meta[name="redfoftplhelper-server-time"]');
        const deltaServer = metaTag ? (new Date(metaTag.getAttribute('content')).getTime() - (new Date()).getTime()) : 0;
        countDown(element, +deltaServer);
    });
});