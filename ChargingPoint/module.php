<?php

declare(strict_types=1);
require_once __DIR__ . '/../libs/helper/VariableProfileHelper.php';

    class ChargingPoint extends IPSModule
    {
        use VariableProfileHelper;

        public function Create()
        {
            //Never delete this line!
            parent::Create();
            $this->ConnectParent('{C6D2AEB3-6E1F-4B2E-8E69-3A1A00246850}');

            $this->RegisterPropertyString('topic', 'openWB');
            $this->RegisterPropertyInteger('lp', 1);

            $this->RegisterProfileIntegerEx('OWB.ChargeLimitation', 'Power', '', '', [
                [0, $this->translate('Off'),  '', -1],
                [1, $this->translate('kWh charge'),  '', -1],
                [2, $this->translate('SoC charge'),  '', -1]
            ]);

            $this->RegisterProfileIntegerEx('OWB.ResetDirectCharge', 'Power', '', '', [
                [1, $this->translate('Reset'),  '', -1],
            ]);

            $this->RegisterProfileInteger('OWB.Ladeleistung', 'Electricity', '', ' A', 6, 32, 1);
            $this->RegisterProfileInteger('OWB.Minuten', '', '', ' Minuten', 0, 0, 1);
            $this->RegisterProfileInteger('OWB.EnergyToCharge', 'Electricity', '', ' kWh', 2, 100, 2);

            $this->RegisterProfileFloat('OWB.KM', '', '', ' km', 0, 0, 1, 1);
            $this->RegisterProfileFloat('OWB.Wh', '', '', ' Wh', 0, 0, 0.1, 1);

            $this->RegisterProfileBooleanEx('OWB.CarPlugged', 'Car', '', '', [
                [false, $this->Translate('Free'),  '', 0xFF0000],
                [true, $this->Translate('Plugged'),  '', 0x00FF00]
            ]);
            $this->RegisterProfileBooleanEx('OWB.ChargeState', 'Car', '', '', [
                [false, $this->Translate('Off'),  '', 0xFF0000],
                [true, $this->Translate('Charge'),  '', 0x00FF00]
            ]);
            $this->RegisterProfileBooleanEx('OWB.ChargePointEnabled', 'Car', '', '', [
                [false, $this->Translate('Locked'),  '', 0xFF0000],
                [true, $this->Translate('Open'),  '', 0xFF0000]
            ]);

            $this->RegisterProfileIntegerEx('OWB.LPState', 'Information', '', '', [
                [0, $this->Translate('Free'), '', 0x00FF00],
                [1, $this->Translate('Blocked'), '', 0xFFFF00],
                [2, $this->Translate('Charge'), '', 0xFF0000],
            ]);

            $this->RegisterVariableInteger('LPSoC', $this->Translate('LP SoC'), '~Intensity.100', 0);
            $this->RegisterVariableInteger('LPAConfigured', $this->Translate('LP Max charge current'), 'OWB.Ladeleistung', 0);
            $this->RegisterVariableInteger('LPAphase1', $this->Translate('LP Phase 1'), 'OWB.Ladeleistung', 0);
            $this->RegisterVariableInteger('LPAphase2', $this->Translate('LP Phase 2'), 'OWB.Ladeleistung', 0);
            $this->RegisterVariableInteger('LPAphase3', $this->Translate('LP Phase 3'), 'OWB.Ladeleistung', 0);
            //AutoLock?
            $this->RegisterVariableBoolean('LPAutolockConfigured', $this->Translate('LP Autolock Configured'), '~Switch', 0);
            $this->RegisterVariableBoolean('LPAutolockStatus', $this->Translate('LP Autolock Status'), '~Switch', 0);

            $this->RegisterVariableBoolean('LPboolChargeAtNight', $this->Translate('LP Charge at night'), '~Switch', 0);
            $this->RegisterVariableBoolean('LPboolChargePointConfigured', $this->Translate('LP Configured'), '~Switch', 0);
            $this->RegisterVariableBoolean('LPboolChargeStat', $this->Translate('LP Charge State'), 'OWB.ChargeState', 0);
            $this->RegisterVariableString('LPboolFinishAtTimeChargeActive', $this->Translate('LP Finish at time charge'), '', 0);
            $this->RegisterVariableBoolean('LPPlugStat', $this->Translate('LP Car plugged'), 'OWB.CarPlugged', 0);
            $this->RegisterVariableBoolean('LPboolSocConfigured', $this->Translate('LP SoC configured'), '~Switch', 0);
            $this->RegisterVariableBoolean('LPboolSoCManual', $this->Translate('LP SOC manual'), '~Switch', 0);
            $this->RegisterVariableBoolean('LPChargePointEnabled', $this->Translate('LP ChargePoint Enabled'), '~Switch', 0);
            $this->EnableAction('LPChargePointEnabled');
            $this->RegisterVariableBoolean('LPChargeStatus', $this->Translate('LP Charge Status'), '~Switch', 0);
            $this->RegisterVariableInteger('LPcountPhasesInUse', $this->Translate('LP Phases in use'), '', 0);

            $this->RegisterVariableFloat('LPenergyConsumptionPer100km', $this->Translate('LP Energy Consumption per 100km'), '~Electricity', 0);
            $this->RegisterVariableInteger('LPfaultState', $this->Translate('LP fault State'), '', 0);
            $this->RegisterVariableFloat('LPkmCharged', $this->Translate('LP km charged'), 'OWB.KM', 0);
            $this->RegisterVariableFloat('LPkWhActualCharged', $this->Translate('LP Actual Charged'), '~Electricity', 0);
            $this->RegisterVariableFloat('LPkWhChargedSincePlugged', $this->Translate('LP Charged since plugged'), '~Electricity', 0);
            $this->RegisterVariableFloat('LPkWhCounter', $this->Translate('LP Counter'), '~Electricity', 0);
            $this->RegisterVariableFloat('LPkWhDailyCharged', $this->Translate('LP Daily Charged'), '~Electricity', 0);
            $this->RegisterVariableString('LPlastRfid', $this->Translate('LP Last RFID'), '', 0);

            //pluggedladungakt
            //plugStartkWh
            //socFaultState
            //socFaultStr
            $this->RegisterVariableString('LPstrChargePointName', $this->Translate('LP Name'), '', 0);
            $this->RegisterVariableString('LPTimeRemaining', $this->Translate('LP Time Remaining'), '', 0);
            $this->RegisterVariableFloat('LPVPhase1', $this->Translate('LP Phase 1 Voltage'), '~Volt', 0);
            $this->RegisterVariableFloat('LPVPhase2', $this->Translate('LP Phase 2 Voltage'), '~Volt', 0);
            $this->RegisterVariableFloat('LPVPhase3', $this->Translate('LP Phase 3 Voltage'), '~Volt', 0);
            $this->RegisterVariableFloat('LPW', $this->Translate('LP Charging Power'), '~Power', 0);

            $this->RegisterVariableInteger('LPCurrent', $this->Translate('LP Current'), 'OWB.Ladeleistung', 0);
            $this->EnableAction('LPCurrent');

            $this->RegisterVariableInteger('LPenergyToCharge', $this->Translate('LP Energy to Charge'), 'OWB.EnergyToCharge', 0);
            $this->EnableAction('LPenergyToCharge');
            $this->RegisterVariableInteger('LPChargeLimitation', $this->Translate('LP Charge Limitation'), 'OWB.ChargeLimitation', 0);
            $this->EnableAction('LPChargeLimitation');
            $this->RegisterVariableInteger('LPresetEnergyToCharge', $this->Translate('LP Reset Energy To Charge'), 'OWB.ResetDirectCharge', 0);
            $this->EnableAction('LPresetEnergyToCharge');
            $this->RegisterVariableInteger('LPsocToChargeTo', $this->Translate('LP SoC to charge to'), '~Intensity.100', 0);
            $this->EnableAction('LPsocToChargeTo');
            $this->RegisterVariableInteger('LPState', $this->Translate('LP State'), 'OWB.LPState', 0);
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
            $lp = $this->ReadPropertyInteger('lp');
            if (!empty($this->ReadPropertyString('topic'))) {
                $this->SendDebug('ReceiveData :: JSON', $JSONString, 0);
                $data = json_decode($JSONString, true);

                //Für MQTT Fix in IPS Version 6.3
                if (IPS_GetKernelDate() > 1670886000) {
                    $data['Payload'] = utf8_decode($data['Payload']);
                }

                switch ($data['Topic']) {
                    case $this->ReadPropertyString('topic') . '/lp/' . $lp . '/%Soc':
                        $this->SetValue('LPSoC', intval($data['Payload']));
                        break;
                    case $this->ReadPropertyString('topic') . '/lp/' . $lp . '/AConfigured':
                        $this->SetValue('LPAConfigured', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/lp/' . $lp . '/APhase1':
                        $this->SetValue('LPAphase1', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/lp/' . $lp . '/APhase2':
                        $this->SetValue('LPAphase2', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/lp/' . $lp . '/APhase3':
                        $this->SetValue('LPAphase3', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/lp/' . $lp . '/AutolockConfigured':
                        $this->SetValue('LPAutolockConfigured', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/lp/' . $lp . '/AutolockStatus':
                        $this->SetValue('LPAutolockStatus', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/lp/' . $lp . '/boolChargeAtNight':
                        $this->SetValue('LPboolChargeAtNight', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/lp/' . $lp . '/boolChargePointConfigured':
                        $this->SetValue('LPboolChargePointConfigured', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/lp/' . $lp . '/boolChargeStat':
                        $this->SetValue('LPboolChargeStat', $data['Payload']);
                        if (!$data['Payload']) {
                            if ($this->GetValue('LPPlugStat')) {
                                $this->SetValue('LPState', 1); //Blocked
                            }
                        } else {
                            if ($this->GetValue('LPPlugStat')) {
                                $this->SetValue('LPState', 2); //Charge
                            }
                        }
                        break;
                    case $this->ReadPropertyString('topic') . '/config/get/sofort/lp/' . $lp . '/chargeLimitation':
                            $this->SetValue('LPChargeLimitation', $data['Payload']);

                        break;
                    case $this->ReadPropertyString('topic') . '/lp/' . $lp . '/boolFinishAtTimeChargeActive':
                        $this->SetValue('LPboolFinishAtTimeChargeActive', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/lp/' . $lp . '/boolPlugStat':
                        switch (intval($data['Payload'])) {
                            case 0:
                                $this->SetValue('LPPlugStat', false);
                                $this->SetValue('LPState', 0); //Frei
                                break;
                            case 1:
                                $this->SetValue('LPPlugStat', true);
                                    break;
                            default:
                                # code...
                                break;
                        }

                        break;
                    case $this->ReadPropertyString('topic') . '/lp/' . $lp . '/boolSocConfigured':
                        $this->SetValue('LPboolSocConfigured', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/lp/' . $lp . '/boolSocManual':
                        $this->SetValue('LPboolSoCManual', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/lp/' . $lp . '/ChargePointEnabled':
                        $this->SetValue('LPChargePointEnabled', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/lp/' . $lp . '/ChargeStatus':
                        $this->SetValue('LPChargeStatus', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/lp/' . $lp . '/countPhasesInUse':
                        $this->SetValue('LPcountPhasesInUse', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/lp/' . $lp . '/enerygConsumptionPer100km':
                        $this->SetValue('LPenergyConsumptionPer100km', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/lp/' . $lp . '/faultState':
                        $this->SetValue('LPfaultState', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/lp/' . $lp . '/kmCharged':
                        $this->SetValue('LPkmCharged', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/lp/' . $lp . '/kWhActualCharged':
                        $this->SetValue('LPkWhActualCharged', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/lp/' . $lp . '/kWhChargedSincePlugged':
                        $this->SetValue('LPkWhChargedSincePlugged', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/lp/' . $lp . '/kWhCounter':
                        $this->SetValue('LPkWhCounter', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/lp/' . $lp . '/kWhDailyCharged':
                        $this->SetValue('LPkWhDailyCharged', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/lp/' . $lp . '/lastRfid':
                        $this->SetValue('LPlastRfid', $data['Payload']);
                        break;
                    //pluggedladungakt
                    //plugStartkWh
                    //socFaultState
                    //socFaultStr
                    case $this->ReadPropertyString('topic') . '/lp/' . $lp . '/strChargePointName':
                        $this->SetValue('LPstrChargePointName', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/lp/' . $lp . '/TimeRemaining':
                        $this->SetValue('LPTimeRemaining', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/lp/' . $lp . '/VPhase1':
                        $this->SetValue('LPVPhase1', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/lp/' . $lp . '/VPhase2':
                        $this->SetValue('LPVPhase2', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/lp/' . $lp . '/VPhase3':
                        $this->SetValue('LPVPhase3', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/lp/' . $lp . '/W':
                        $this->SetValue('LPW', $data['Payload'] / 1000);
                        break;
                    case $this->ReadPropertyString('topic') . '/config/get/sofort/lp/' . $lp . '/current':
                        $this->SetValue('LPCurrent', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/config/get/sofort/lp/' . $lp . '/socToChargeTo':
                        $this->SetValue('LPsocToChargeTo', $data['Payload']);
                        break;
                    case $this->ReadPropertyString('topic') . '/config/get/sofort/lp/' . $lp . '/energyToCharge':
                        $this->SetValue('LPenergyToCharge', $data['Payload']);
                        break;

                }
            }
        }

        public function RequestAction($Ident, $Value)
        {
            $lp = $this->ReadPropertyInteger('lp');
            switch ($Ident) {
                case 'LPChargePointEnabled':
                    $this->MQTTCommand('set/lp/' . $lp . '/ChargePointEnabled', intval($Value));
                    break;
                case 'LPCurrent':
                    $this->MQTTCommand('config/set/sofort/lp/' . $lp . '/current', intval($Value));
                    break;
                case 'LPenergyToCharge':
                    $this->MQTTCommand('config/set/sofort/lp/' . $lp . '/energyToCharge', intval($Value));
                    break;
                case 'LPChargeLimitation':
                    $this->MQTTCommand('config/set/sofort/lp/' . $lp . '/chargeLimitation', intval($Value));
                    break;
                case 'LPresetEnergyToCharge':
                    $this->MQTTCommand('config/set/sofort/lp/' . $lp . '/resetEnergyToCharge', intval($Value));
                    break;
                case 'LPsocToChargeTo':
                    $this->MQTTCommand('config/set/sofort/lp/' . $lp . '/socToChargeTo', intval($Value));
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
