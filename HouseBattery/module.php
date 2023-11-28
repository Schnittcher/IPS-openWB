<?php

declare(strict_types=1);
require_once __DIR__ . '/../libs/helper/VariableProfileHelper.php';

    class HouseBattery extends IPSModule
    {
        use VariableProfileHelper;

        public function Create()
        {
            //Never delete this line!
            parent::Create();
            $this->ConnectParent('{C6D2AEB3-6E1F-4B2E-8E69-3A1A00246850}');
            $this->RegisterPropertyString('topic', 'openWB');

            $this->RegisterVariableInteger('SoC', $this->Translate('SoC'), '~Intensity.100', 0);
            $this->EnableAction('SoC');
            $this->RegisterVariableBoolean('boolHouseBatteryConfigured', $this->Translate('House Battery Configured'), '~Switch', 0);
            $this->RegisterVariableFloat('DailyYieldExportKwh', $this->Translate('Daily Yield Export'), '~Electricity', 0);
            $this->RegisterVariableFloat('DailyYieldImportKwh', $this->Translate('Daily Yield Import'), '~Electricity', 0);
            $this->RegisterVariableInteger('faultState', $this->Translate('Fault State'), '', 0);
            $this->RegisterVariableString('faultStr', $this->Translate('Fault State'), '', 0);
            $this->RegisterVariableFloat('W', $this->Translate('Power'), '~Watt', 0);
            $this->EnableAction('W');
            $this->RegisterVariableFloat('WhExported', $this->Translate('Wh Exported'), '~Electricity.Wh', 0);
            $this->EnableAction('WhExported');
            $this->RegisterVariableFloat('WhExported_temp', $this->Translate('Wh Exported Temp'), '', 0);
            $this->RegisterVariableFloat('WhImported', $this->Translate('Wh Imported'), '~Electricity.Wh', 0);
            $this->EnableAction('WhImported');
            $this->RegisterVariableFloat('WhImported_temp', $this->Translate('Wh Imported Temp'), '', 0);
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
            if (!empty($this->ReadPropertyString('topic'))) {
                $this->SendDebug('ReceiveData :: JSON', $JSONString, 0);
                $data = json_decode($JSONString, true);

                //Für MQTT Fix in IPS Version 6.3
                if (IPS_GetKernelDate() > 1670886000) {
                    $data['Payload'] = utf8_decode($data['Payload']);
                }

                switch ($data['Topic']) {
                    case $this->ReadPropertyString('topic') . '/housebattery/%Soc':
                        $this->SetValue('SoC', intval($data['Payload']));
                        break;
                    case $this->ReadPropertyString('topic') . '/housebattery/boolHouseBatteryConfigured':
                        $this->SetValue('boolHouseBatteryConfigured', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/housebattery/DailyYieldExportKwh':
                        $this->SetValue('DailyYieldExportKwh', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/housebattery/DailyYieldImportKwh':
                        $this->SetValue('DailyYieldImportKwh', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/housebattery/faultState':
                        $this->SetValue('faultState', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/housebattery/faultStr':
                        $this->SetValue('faultStr', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/housebattery/W':
                        $this->SetValue('W', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/housebattery/WhExported':
                        $this->SetValue('WhExported', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/housebattery/WhExported_temp':
                        $this->SetValue('WhExported_temp', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/housebattery/WhImported':
                        $this->SetValue('WhImported', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/housebattery/WhImported_temp':
                        $this->SetValue('WhImported_temp', $data['Payload']);
                        break;
                    default:
                        break;
                }
            }
        }

        public function RequestAction($Ident, $Value)
        {
            $lp = $this->ReadPropertyInteger('lp');
            switch ($Ident) {
                case 'SoC':
                    $this->MQTTCommand('set/houseBattery/%Soc', intval($Value));
                    break;
                case 'W':
                    $this->MQTTCommand('set/houseBattery/W', floatval($Value));
                    break;
                case 'WhExported':
                    $this->MQTTCommand('set/houseBattery/WhExported', floatval($Value));
                    break;
                case 'WhImported':
                    $this->MQTTCommand('set/houseBattery/WhImported', floatval($Value));
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
