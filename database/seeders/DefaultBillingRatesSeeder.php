<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DefaultBillingRates;
class DefaultBillingRatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DefaultBillingRates::create([
            'item_name'=>'SIClytics_user',
            'description'=>'SIClytics User Login',
            'discount_item'=>'0',
            'discount_value'=>'0',
            'rate'=>'0'
        ]);

        DefaultBillingRates::create([
            'item_name'=>'Phone_terminal_extension',
            'description'=>'Phone Terminal: The Phone terminal extension is used to connect phone devices to the system. This extension has a couple of features that you can use to manage your business and are not available.',
            'discount_item'=>'0',
            'discount_value'=>'0',
            'rate'=>'0'
        ]);
        DefaultBillingRates::create([
            'item_name'=>'Queue_extension',
            'description'=>'Phone Queue : Queue extensions are used in call centers along with phone terminal and IVR extensions.',
            'discount_item'=>'0',
            'discount_value'=>'0',
            'rate'=>'0'
        ]);
        DefaultBillingRates::create([
            'item_name'=>'Queue_center_extension',
            'description'=>'Queue Center : Remote agents can log in to a queue using the Queue Login Center extension.',
            'discount_item'=>'0',
            'discount_value'=>'0',
            'rate'=>'0'
        ]);
        DefaultBillingRates::create([
            'item_name'=>'Auto attendant extension',
            'description'=>'IVR : The Interactive Voice Response (IVR) extension, also known as the automated attendant, automates interactions with telephone callers, allowing for customized menus and user scenarios.',
            'discount_item'=>'0',
            'discount_value'=>'0',
            'rate'=>'0'
        ]);
        DefaultBillingRates::create([
            'item_name'=>'voicemail_extension',
            'description'=>'Voicemail Center : The Voicemail Center extension resembles the Voicemail function of a Phone Terminal extension.',
            'discount_item'=>'0',
            'discount_value'=>'0',
            'rate'=>'0'
        ]);
        DefaultBillingRates::create([
            'item_name'=>'conference_extension',
            'description'=>'Conference : A Conference extension allows users calling from phones connected to the system as well as callers outside the system (calling from a phone connected to the PSTN) to attend a group voice conversation.',
            'discount_item'=>'0',
            'discount_value'=>'0',
            'rate'=>'0'
        ]);

          DefaultBillingRates::create([
            'item_name'=>'callback_extension',
            'description'=>'Callback : System users can call a Callback extension from a phone connected to the public network and, through it, place an outgoing call using the VoipNow platform.',
            'discount_item'=>'0',
            'discount_value'=>'0',
            'rate'=>'0'
        ]);

          DefaultBillingRates::create([
            'item_name'=>'calling_card_extension',
            'description'=>'Calling Card : This type of extension allows setting up a calling card service on the system. This way customers can use a calling card to pay for telephone services, even if their phone is not connected to the VoipNow system.',
            'discount_item'=>'0',
            'discount_value'=>'0',
            'rate'=>'0'
        ]);
          DefaultBillingRates::create([
            'item_name'=>'intercom_extension',
            'description'=>"Intercom Paging : The Intercom/Paging extension allows users to call several or all available extensions at the same time. The called extensions will hear the caller's announcements no matter if they pick up the phone or not.",
            'discount_item'=>'0',
            'discount_value'=>'0',
            'rate'=>'0'
        ]);
          DefaultBillingRates::create([
            'item_name'=>'registered_e911_number',
            'description'=>'Telecom - e911Provosioning',
            'discount_item'=>'0',
            'discount_value'=>'0',
            'rate'=>'0'
        ]);
          DefaultBillingRates::create([
            'item_name'=>'Active_dids',
            'description'=>'Telecom - DID Telephone Number(s)',
            'discount_item'=>'0',
            'discount_value'=>'0',
            'rate'=>'0'
        ]);
          DefaultBillingRates::create([
            'item_name'=>'Sip_trunks',
            'description'=>'SIP Trunking Channel',
            'discount_item'=>'0',
            'discount_value'=>'0',
            'rate'=>'0'
        ]);
          DefaultBillingRates::create([
            'item_name'=>'storage_rate',
            'description'=>'Audio Recording Storage (gig)',
            'discount_item'=>'0',
            'discount_value'=>'0',
            'rate'=>'0'
        ]);

              DefaultBillingRates::create([
            'item_name'=>'calls_incoming',
            'description'=>'CALLS INCOMING : All incoming call charges including any incoming calls to your toll free numbers.',
            'discount_item'=>'0',
            'discount_value'=>'0',
            'rate'=>'0'
        ]);
        DefaultBillingRates::create([
            'item_name'=>'calls_outgoing',
            'description'=>'CALLS OUTGOING: All outgoing calls including international calls or calls outside your package.',
            'discount_item'=>'0',
            'discount_value'=>'0',
            'rate'=>'0'
        ]);
            DefaultBillingRates::create([
            'item_name'=>'Dafeeder_users',
            'description'=>'DaFeeder User Login',
            'discount_item'=>'0',
            'discount_value'=>'0',
            'rate'=>'0'
        ]);
            DefaultBillingRates::create([
            'item_name'=>'incoming_cost',
            'description'=>'Incoming Cost assigned to phone numbers',
            'discount_item'=>'0',
            'discount_value'=>'0',
            'rate'=>'0'
        ]);

            DefaultBillingRates::create([
            'item_name'=>'Did_cost',
            'description'=>'DID Price when buying from siclytics',
            'discount_item'=>'0',
            'discount_value'=>'0',
            'rate'=>'0'
        ]);
            DefaultBillingRates::create([
            'item_name'=>'SMS_outgoing',
            'description'=>'Outgoing SMS Cost',
            'discount_item'=>'0',
            'discount_value'=>'0',
            'rate'=>'0'
        ]);
            DefaultBillingRates::create([
            'item_name'=>'SMS_incoming',
            'description'=>'Incoming SMS Cost',
            'discount_item'=>'0',
            'discount_value'=>'0',
            'rate'=>'0'
        ]);
            DefaultBillingRates::create([
            'item_name'=>'Incoming SMS Cost',
            'description'=>'Incoming SMS Cost',
            'discount_item'=>'0',
            'discount_value'=>'0',
            'rate'=>'0'
        ]);
             DefaultBillingRates::create([
            'item_name'=>'MMS_outgoing',
            'description'=>'Outgoing MMS Cost',
            'discount_item'=>'0',
            'discount_value'=>'0',
            'rate'=>'0'
        ]);
              DefaultBillingRates::create([
            'item_name'=>'MMS_Incoming',
            'description'=>'Incoming MMS Cost',
            'discount_item'=>'0',
            'discount_value'=>'0',
            'rate'=>'0'
        ]);

              DefaultBillingRates::create([
            'item_name'=>'SMS_mms_numbers',
            'description'=>'SMS/MMS DID Cost',
            'discount_item'=>'0',
            'discount_value'=>'0',
            'rate'=>'0'
        ]);

    }
}
