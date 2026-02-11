<?php

declare(strict_types=1);
require_once __DIR__ . '/../libs/helper/VariableProfileHelper.php';

    class openWB extends IPSModule
    {
        use VariableProfileHelper;

        public function Create()
        {
            //Never delete this line!
            parent::Create();
            $this->RegisterPropertyString('Host', '');
            $this->RegisterPropertyInteger('UpdateInterval', 10);

            $this->RegisterTimer('OPENWB_UpdateState', 0, 'OPENWB_UpdateState($_IPS[\'TARGET\']);');

            $this->RegisterProfileIntegerEx('OWB.Lademodus', 'Power', '', '', [
                [0, $this->translate('Immediately'),  '', -1],
                [1, $this->translate('Min+PV'),  '', -1],
                [2, $this->translate('Only PV'),  '', -1],
                [3, $this->translate('Standby'),  '', -1],
                [4, $this->translate('Stop'),  '', -1]
            ]);

            $this->RegisterProfileInteger('OWB.Ladeleistung', 'Electricity', '', ' A', 6, 32, 1);
            $this->RegisterProfileInteger('OWB.Minuten', '', '', ' Minuten', 0, 0, 1);
            $this->RegisterProfileFloat('OWB.KM', '', '', ' km', 0, 0, 1, 1);
            $this->RegisterProfileFloat('OWB.Wh', '', '', ' Wh', 0, 0, 0.1, 1);

            $this->RegisterVariableInteger('lademodus', $this->Translate('Loading Mode'), 'OWB.Lademodus', 0);
            $this->EnableAction('lademodus');

            $this->RegisterVariableInteger('sofortlllp1', $this->Translate('Loading Capacity (lp1)'), 'OWB.Ladeleistung', 0);
            $this->EnableAction('sofortlllp1');

            $this->RegisterVariableInteger('sofortlllp2', $this->Translate('Loading Capacity (lp2)'), 'OWB.Ladeleistung', 0);
            $this->EnableAction('sofortlllp2');

            $this->RegisterVariableInteger('sofortlllp3', $this->Translate('Loading Capacity (lp3)'), 'OWB.Ladeleistung', 0);
            $this->EnableAction('sofortlllp3');

            $this->RegisterVariableFloat('minimalstromstaerke', $this->Translate('Minimal Amperage'), '~Ampere', 0);
            $this->RegisterVariableFloat('maximalstromstaerke', $this->Translate('Maximum Amperage'), '~Ampere', 0);
            $this->RegisterVariableInteger('llsoll', $this->Translate('Target charging current specification'), '', 0);
            $this->RegisterVariableString('restzeitlp1', $this->Translate('Restzeit (lp1)'), '', 0);
            $this->RegisterVariableString('restzeitlp2', $this->Translate('Restzeit (lp2)'), '', 0);
            $this->RegisterVariableString('restzeitlp3', $this->Translate('Restzeit (lp3)'), '', 0);

            $this->RegisterVariableFloat('gelkwhlp1', $this->Translate('In the current loading process (lp1)'), '~Electricity', 0);
            $this->RegisterVariableFloat('gelkwhlp2', $this->Translate('In the current loading process (lp2)'), '~Electricity', 0);
            $this->RegisterVariableFloat('gelkwhlp3', $this->Translate('In the current loading process (lp3)'), '~Electricity', 0);

            $this->RegisterVariableFloat('gelrlp1', $this->Translate('km loaded in the current charging process (lp1)'), 'OWB.KM', 0);
            $this->RegisterVariableFloat('gelrlp2', $this->Translate('km loaded in the current charging process (lp2)'), 'OWB.KM', 0);
            $this->RegisterVariableFloat('gelrlp3', $this->Translate('km loaded in the current charging process (lp3)'), 'OWB.KM', 0);

            $this->RegisterVariableFloat('llgesamt', $this->Translate('Total charging power of all charging points'), '', 0);

            $this->RegisterVariableFloat('evua1', $this->Translate('Ampere reference at the EVU (1)'), '~Ampere', 0);
            $this->RegisterVariableFloat('evua2', $this->Translate('Ampere reference at the EVU (2)'), '~Ampere', 0);
            $this->RegisterVariableFloat('evua3', $this->Translate('Ampere reference at the EVU (3)'), '~Ampere', 0);

            $this->RegisterVariableFloat('lllp1', $this->Translate('Charging Power (lp1)'), '~Watt.14490', 0);
            $this->RegisterVariableFloat('lllp2', $this->Translate('Charging Power (lp2)'), '~Watt.14490', 0);
            $this->RegisterVariableFloat('lllp3', $this->Translate('Charging Power (lp3)'), '~Watt.14490', 0);

            $this->RegisterVariableFloat('evuw', $this->Translate('Withdrawal / excess at the EVU'), '~Watt.14490', 0);
            $this->RegisterVariableFloat('pvw', $this->Translate('PV power'), '~Watt.14490', 0);

            $this->RegisterVariableFloat('evuv1', $this->Translate('Volts at the EVU (1)'), '~Volt', 0);
            $this->RegisterVariableFloat('evuv2', $this->Translate('Volts at the EVU (2)'), '~Volt', 0);
            $this->RegisterVariableFloat('evuv3', $this->Translate('Volts at the EVU (3)'), '~Volt', 0);

            $this->RegisterVariableBoolean('ladestatusLP1', $this->Translate('Charging Status (lp1)'), '', 0);
            $this->RegisterVariableBoolean('ladestatusLP2', $this->Translate('Charging Status (lp2)'), '', 0);
            $this->RegisterVariableBoolean('ladestatusLP3', $this->Translate('Charging Status (lp3)'), '', 0);

            $this->RegisterVariableString('zielladungaktiv', $this->Translate('Destination Charge active'), '', 0);

            $this->RegisterVariableFloat('lla1LP1', $this->Translate('Ampere 1 (lp1)'), '~Ampere', 0);
            $this->RegisterVariableFloat('lla2LP1', $this->Translate('Ampere 2 (lp1)'), '~Ampere', 0);
            $this->RegisterVariableFloat('lla3LP1', $this->Translate('Ampere 3 (lp1)'), '~Ampere', 0);

            $this->RegisterVariableFloat('lla1LP2', $this->Translate('Ampere 1 (lp2)'), '~Ampere', 0);
            $this->RegisterVariableFloat('lla2LP2', $this->Translate('Ampere 2 (lp2)'), '~Ampere', 0);
            $this->RegisterVariableFloat('lla3LP2', $this->Translate('Ampere 3 (lp2)'), '~Ampere', 0);

            $this->RegisterVariableFloat('lla1LP3', $this->Translate('Ampere 1 (lp3)'), '~Ampere', 0);
            $this->RegisterVariableFloat('lla2LP3', $this->Translate('Ampere 2 (lp3)'), '~Ampere', 0);
            $this->RegisterVariableFloat('lla3LP3', $this->Translate('Ampere 3 (lp3)'), '~Ampere', 0);

            $this->RegisterVariableFloat('llkwhLP1', $this->Translate('Meter Reading (lp1)'), '~Electricity', 0);
            $this->RegisterVariableFloat('llkwhLP2', $this->Translate('Meter Reading (lp2)'), '~Electricity', 0);
            $this->RegisterVariableFloat('llkwhLP3', $this->Translate('Meter Reading (lp3)'), '~Electricity', 0);

            $this->RegisterVariableFloat('evubezugWh', $this->Translate('Meter reading reference'), 'OWB.Wh', 0);
            $this->RegisterVariableFloat('evueinspeisungWh', $this->Translate('Meter reading infeed'), 'OWB.Wh', 0);
            $this->RegisterVariableFloat('pvWh', $this->Translate('Meter reading PV'), 'OWB.Wh', 0);

            $this->RegisterVariableInteger('speichersoc', $this->Translate('SoC of storage'), '~Intensity.100', 0);
            $this->RegisterVariableInteger('socLP1', $this->Translate('SoC EV (lp1)'), '~Intensity.100', 0);
            $this->RegisterVariableInteger('socLP2', $this->Translate('SoC EV (lp2)'), '~Intensity.100', 0);

            $this->RegisterVariableFloat('ladungaktivLP1', $this->Translate('Charge active (lp1)'), '', 0);
            $this->RegisterVariableFloat('ladungaktivLP2', $this->Translate('Charge active (lp2)'), '', 0);
            $this->RegisterVariableFloat('ladungaktivLP3', $this->Translate('Charge active (lp3)'), '', 0);

            $this->RegisterVariableFloat('chargestatLP1', $this->Translate('Chargestat (lp1)'), '', 0);
            $this->RegisterVariableFloat('chargestatLP2', $this->Translate('Chargestat (lp2)'), '', 0);

            $this->RegisterVariableFloat('plugstatLP1', $this->Translate('Plugstat (lp1)'), '', 0);
            $this->RegisterVariableFloat('plugstatLP2', $this->Translate('Plugstat (lp2)'), '', 0);

            $this->RegisterVariableInteger('restzeitlp1m', $this->Translate('Rest Time (lp1)'), 'OWB.Minuten', 0);
            $this->RegisterVariableInteger('restzeitlp2m', $this->Translate('Rest Time (lp2)'), 'OWB.Minuten', 0);
            $this->RegisterVariableInteger('restzeitlp3m', $this->Translate('Rest Time (lp3)'), 'OWB.Minuten', 0);

            $this->RegisterVariableFloat('speicherleistung', $this->Translate('Memory Performance'), '', 0);
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
            if ($this->ReadPropertyString('Host') != '') {
                $this->SetTimerInterval('OPENWB_UpdateState', $this->ReadPropertyInteger('UpdateInterval') * 1000);
            }
        }

        public function RequestAction($Ident, $Value)
        {
            switch ($Ident) {
                case 'lademodus':
                        switch ($Value) {
                            case 0:
                                $this->sendRequest('lademodus', 'jetzt');
                                break;
                            case 1:
                                $this->sendRequest('lademodus', 'minundpv');
                                break;
                            case 2:
                                $this->sendRequest('lademodus', 'pvuberschuss');
                                break;
                            case 3:
                                $this->SendDebug(__FUNCTION__, 'Standby kann nicht ausgewählt werden!', 0);
                                break;
                            case 4:
                                $this->sendRequest('lademodus', 'stop');
                                break;
                        }
                    break;
                case 'sofortlllp1':
                    $this->sendRequest('sofortlllp1', $Value);
                    break;
                case 'sofortlllp2':
                    $this->sendRequest('sofortlllp2', $Value);
                    break;
                case 'sofortlllp3':
                    $this->sendRequest('sofortlllp3', $Value);
                    break;
                default:
                    $this->LogMessage('Invalid Action', KL_WARNING);
                    break;
            }
        }

        public function UpdateState()
        {
            $result = $this->sendRequest('get', 'all');

            foreach ($result as $key => $value) {
                if (@$this->GetIDForIdent($key) != false) {
                    $this->SendDebug($this->GetIDForIdent($key), 'Key: ' . $key . ' - Value: ' . $value, 0);
                    // Variablentyp (0: Boolean, 1: Integer, 2: Float, 3: String)
                    switch(IPS_GetVariable($this->GetIDForIdent($key))['VariableType'])
                    {
                        case 1:
                            $this->SetValue($key, (int)$value);
                            break;
                        case 2:
                            $this->SetValue($key, (float)$value);
                            break;
                        default:
                            $this->SetValue($key, $value);
                            break;
                    }
                } else {
                    $this->SendDebug('Variable not exist', 'Key: ' . $key . ' - Value: ' . $value, 0);
                }
            }
        }

        private function sendRequest(string $endpoint, $value)
        {
            if ($this->ReadPropertyString('Host') == '') {
                return false;
            }
            $ch = curl_init();
            if ($endpoint != '') {
                $url = $this->ReadPropertyString('Host') . '/openWB/web/api.php?' . $endpoint . '=' . $value;
                $this->SendDebug(__FUNCTION__ . ' URL', $url, 0);
                curl_setopt($ch, CURLOPT_URL, $url);
            }

            curl_setopt($ch, CURLOPT_USERAGENT, 'Symcon');
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $apiResult = curl_exec($ch);
            $this->SendDebug(__FUNCTION__ . ' Result', $apiResult, 0);

            $headerInfo = curl_getinfo($ch);
            if ($headerInfo['http_code'] == 200) {
                if ($apiResult != false) {
                    $this->SetStatus(102);
                    return json_decode($apiResult, false);
                } else {
                    $this->LogMessage('openWbConnect sendRequest Error' . curl_error($ch), 10205);
                    $this->SetStatus(201);
                }
            } else {
                $this->LogMessage('openWbConnect sendRequest Error - Curl Error:' . curl_error($ch) . 'HTTP Code: ' . $headerInfo['http_code'], 10205);
                $this->SetStatus(202);
            }
            curl_close($ch);
        }
    }
