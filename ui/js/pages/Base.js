import {AutocompleteBlock} from '../parts/AutocompleteBlock.js';
import {Tooltip}           from '../parts/Tooltip.js?v=2';

export class BasePage {
    constructor() {
        this.initCommonUi();
    }

    initCommonUi() {
        const autocomplete = new AutocompleteBlock();
        document.addEventListener('keyup', e => {
            if (e.key !== '/' || e.ctrlKey || e.metaKey) return;
            if (/^(?:input|textarea|select|button)$/i.test(e.target.tagName)) return;

            e.preventDefault();
            document.getElementById('auto-complete').focus();
        });

        const initTooltip = this.initTooltip;
        if(initTooltip) {
            Tooltip.init();
        }

        const notificationBlock = this.notificationBlock;
        if(notificationBlock) {
            const onCloseNotificationButtonClick = this.onCloseNotificationButtonClick.bind(this);
            this.closeNotificationButton.addEventListener('click', onCloseNotificationButtonClick, false);
        }

        const procedureNotification = this.procedureNotification;
        if (procedureNotification) {
            const onCloseProcedureNotificationButtonClick = this.onCloseProcedureNotificationButtonClick.bind(this);
            this.closeProcedureNotificationButton.addEventListener('click', onCloseProcedureNotificationButtonClick, false);
        }
    }

    onCloseNotificationButtonClick() {
        this.notificationBlock.remove();
    }

    onCloseProcedureNotificationButtonClick() {
        this.procedureNotification.remove();
    }

    get initTooltip() {
        return true;
    }

    get notificationBlock() {
        return document.querySelector('.notification.system:not(.is-hidden)');
    }

    get closeNotificationButton() {
        return document.querySelector('.notification.system:not(.is-hidden) .delete');
    }

    get procedureNotification() {
        return document.querySelector('#procedure-notification');
    }

    get closeProcedureNotificationButton() {
        return document.querySelector('#procedure-notification .delete');
    }
}
