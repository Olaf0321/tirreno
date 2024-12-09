import * as Renderers from '../DataRenderers.js?v=2';
import {BasePanel} from './BasePanel.js?v=2';

export class EmailPanel extends BasePanel {

    constructor() {
        let eventParams = {
            enrichment: true,
            type: 'email',
            url: '/admin/emailDetails',
            cardId: 'email-card',
            panelClosed: 'emailPanelClosed',
            closePanel: 'closeEmailPanel',
            rowClicked: 'emailTableRowClicked',
        };
        super(eventParams);
    }

    proceedData(data) {
        data.email                  = Renderers.renderEmail(data, 'long');
        data.reputation             = Renderers.renderReputation(data);

        // to 'No breach'
        data.data_breach            = Renderers.renderBoolean(data.data_breach === null ? null : !data.data_breach);
        // to 'No Profiles'
        // data.profiles               = Renderers.renderBoolean(data.profiles === null ? null : data.profiles === 0);
        data.data_breaches          = Renderers.renderDefaultIfEmpty(data.data_breaches);

        data.earliest_breach        = Renderers.renderDate(data.earliest_breach);
        data.fraud_detected         = Renderers.renderBoolean(data.fraud_detected);
        data.blockemails            = Renderers.renderBoolean(data.blockemails);
        //  TODO: return alert_list back in next release
        //data.alert_list           = Renderers.renderBoolean(data.alert_list);
        data.domain_contact_email   = Renderers.renderBoolean(data.domain_contact_email);

        data.free_email_provider    = Renderers.renderBoolean(data.free_email_provider);

        const domain_record = {
            domain:     data.domain,
            id:         data.domain_id,
        };
        data.domain                 = Renderers.renderClickableDomain(domain_record, 'long');
        data.blockdomains           = Renderers.renderBoolean(data.blockdomains);
        data.disabled               = Renderers.renderBoolean(data.disabled);
        data.mx_record              = Renderers.renderBoolean(data.mx_record === null ? null : !data.mx_record);
        data.disposable_domains     = Renderers.renderBoolean(data.disposable_domains);
        data.disabled               = Renderers.renderBoolean(data.disabled);
        data.tranco_rank            = Renderers.renderDefaultIfEmpty(data.tranco_rank);
        data.creation_date          = Renderers.renderDate(data.creation_date);
        data.expiration_date        = Renderers.renderDate(data.expiration_date);
        data.closest_snapshot       = Renderers.renderDate(data.closest_snapshot);
        data.return_code            = Renderers.renderHttpCode({http_code: data.return_code});

        // also data.checked is used

        return data;
    }
}
