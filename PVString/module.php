<?php

declare(strict_types=1);
require_once __DIR__ . '/../libs/helper/VariableProfileHelper.php';

    class PVString extends IPSModule
    {
        use VariableProfileHelper;

        public function Create()
        {
            //Never delete this line!
            parent::Create();
            $this->ConnectParent('{C6D2AEB3-6E1F-4B2E-8E69-3A1A00246850}');
            $this->RegisterPropertyString('topic', 'openWB');
            $this->RegisterPropertyInteger('pv', 1);
            $this->RegisterVariableBoolean('bool70PVDynActive', $this->Translate('PV 70 Dyn Active'), '~Switch', 0);
            $this->RegisterVariableBoolean('bool70PVDynStatus', $this->Translate('PV 70 Dyn Status'), '~Switch', 0);
            $this->RegisterVariableFloat('CounterTillStartPvCharging', $this->Translate('Counter Till Start Pv Charging'), '', 0);
            $this->RegisterVariableFloat('DailyYieldKwh', $this->Translate('Daily Yield'), '~Electricity', 0);
            $this->RegisterVariableFloat('MonthlyYieldKwh', $this->Translate('Monthly Yield'), '~Electricity', 0);
            $this->RegisterVariableFloat('W', $this->Translate('Power'), '~Watt', 0);
            $this->EnableAction('W');
            $this->RegisterVariableFloat('W70PVDyn', $this->Translate('W 70 PV Dyn'), '~Watt', 0);
            $this->RegisterVariableFloat('WhCounter', $this->Translate('Wh Counter'), '~Electricity.Wh', 0);
            $this->EnableAction('WhCounter');
            $this->RegisterVariableFloat('WHExport_temp', $this->Translate('Wh Export temp'), '', 0);
            $this->RegisterVariableFloat('WHImported_temp', $this->Translate('Wh Imported temp'), '', 0);
            $this->RegisterVariableFloat('YearlyYieldKwh', $this->Translate('Yearly Yield'), '~Electricity', 0);
        }

        public function Destroy()
        {
            //Never delete this line!
            parent::Destroy();
        }

        public function ApplyChanges()
        {
            //Never delete this line!
            parent::ApplyChanges();
            $this->ConnectParent('{C6D2AEB3-6E1F-4B2E-8E69-3A1A00246850}');

            //Setze Filter für ReceiveData
            $MQTTTopic = $this->ReadPropertyString('topic');
            $this->SetReceiveDataFilter('.*' . $MQTTTopic . '.*');
        }

        public function ReceiveData($JSONString)
        {
            $pv = $this->ReadPropertyInteger('pv');
            if (!empty($this->ReadPropertyString('topic'))) {
                $this->SendDebug('ReceiveData :: JSON', $JSONString, 0);
                $data = json_decode($JSONString, true);

                //Für MQTT Fix in IPS Version 6.3
                if (IPS_GetKernelDate() > 1670886000) {
                    $data['Payload'] = utf8_decode($data['Payload']);
                }

                switch ($data['Topic']) {
                    case $this->ReadPropertyString('topic') . '/pv/' . $pv . '/' . $pv . '/bool70PVDynActive':
                        $this->SetValue('bool70PVDynActive', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/pv/' . $pv . '/bool70PVDynStatus':
                        $this->SetValue('bool70PVDynStatus', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/pv/' . $pv . '/CounterTillStartPvCharging':
                        $this->SetValue('CounterTillStartPvCharging', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/pv/' . $pv . '/DailyYieldKwh':
                        $this->SetValue('DailyYieldKwh', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/pv/' . $pv . '/MonthlyYieldKwh':
                        $this->SetValue('MonthlyYieldKwh', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/pv/' . $pv . '/W':
                        $this->SetValue('W', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/pv/' . $pv . '/W70PVDyn':
                        $this->SetValue('W70PVDyn', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/pv/' . $pv . '/WhCounter':
                        $this->SetValue('WhCounter', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/pv/' . $pv . '/WHExport_temp':
                        $this->SetValue('WHExport_temp', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/pv/' . $pv . '/WHImported_temp':
                        $this->SetValue('WHImported_temp', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/pv/' . $pv . '/YearlyYieldKwh':
                        $this->SetValue('YearlyYieldKwh', $data['Payload']);
                        break;
                    default:
                        break;
                }
            }
        }
        public function RequestAction($Ident, $Value)
        {
            $pv = $this->ReadPropertyInteger('pv');
            switch ($Ident) {
                case 'W':
                    $this->MQTTCommand('set/pv/' . $pv . '/W', floatval($Value));
                    break;
                case 'WhCounter':
                    $this->MQTTCommand('set/pv/' . $pv . '/WhCounter', floatval($Value));
                    break;
                default:
                    $this->LogMessage('Invalid Action', KL_WARNING);
                    break;
            }
        }

        private function MQTTCommand($Topic, $Payload, $retain = 0)
        {
            $Topic = $this->ReadPropertyString('topic') . '/' . $Topic;
            $Data['DataID'] = '{043EA491-0325-4ADD-8FC2-A30C8EEB4D3F}';
            $Data['PacketType'] = 3;
            $Data['QualityOfService'] = 0;
            $Data['Retain'] = boolval($retain);
            $Data['Topic'] = $Topic;
            $Data['Payload'] = strval($Payload);
            $JSON = json_encode($Data, JSON_UNESCAPED_SLASHES);
            $result = @$this->SendDataToParent($JSON);

            if ($result === false) {
                $last_error = error_get_last();
                echo $last_error['message'];
            }
        }
    }
