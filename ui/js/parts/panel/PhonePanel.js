import * as Renderers from '../DataRenderers.js?v=2';
import {BasePanel} from './BasePanel.js?v=2';

export class PhonePanel extends BasePanel {

    constructor() {
        let eventParams = {
            enrichment: true,
            type: 'phone',
            url: '/admin/phoneDetails',
            cardId: 'phone-card',
            panelClosed: 'phonePanelClosed',
            closePanel: 'closePhonePanel',
            rowClicked: 'phoneTableRowClicked',
        };
        super(eventParams);
    }

    proceedData(data) {
        const phone_record = {
            phonenumber:    data.phone_number,
            country:        data.phone_country,
            full_country:   data.phone_full_country,
            carrier_name:   data.carrier_name,
            type:           data.type
        };
        data.phone_number           = Renderers.renderPhone(phone_record);
        data.phone_national         = Renderers.renderDefaultIfEmpty(data.national_format);
        data.country                = Renderers.renderFullCountry(data.phone_full_country);
        data.carrier_name           = Renderers.renderPhoneCarrierName(phone_record);
        data.type                   = Renderers.renderPhoneType(phone_record);
        data.shared                 = Renderers.renderUserCounter(data.shared, 2);

        // to 'No Profiles'
        //data.profiles               = Renderers.renderBoolean(data.profiles === null ? null : data.profiles === 0);

        data.fraud_detected         = Renderers.renderBoolean(data.fraud_detected);
        data.invalid                = Renderers.renderBoolean(data.invalid);
        //  TODO: return alert_list back in next release
        //data.alert_list           = Renderers.renderBoolean(data.alert_list);

        data.shared_users           = Renderers.renderUsersList(data.shared_users);

        // also data.checked is used

        return data;
    }
}
