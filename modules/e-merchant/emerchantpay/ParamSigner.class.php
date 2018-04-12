<?php
class ParamSigner {
    var $secret;
    var $params;
    var $lifetime=24;
    var $signatureType='PSMD5';

    function setSecret($secret)
    {
        $this->secret=$secret;
    }

    function setLifeTime($lifetime)
    {
        $this->lifetime=$lifetime;
    }

    function setSignatureType($signatureType)
    {
        if ($this->_checkSignatureType($signatureType)){
            $this->signatureType=$signatureType;
        }else{
            throw new exception ("Invalid signatureType : $signatureType");
        }
    }

    function setParam ($param,$value)
    {
        if ($param!='PS_SIGNATURE')	$this->params[$param]=$value;
    }

    function setParams ($paramArray)
    {
        foreach ($paramArray as $param=>$value){
            $this->setParam($param,$value);
        }
    }

    function getQueryString()
    {
        return $this->getSignature(true);
    }

    function getSignedParams ()
    {
        $Sig = $this->getSignature();
        return $this->params + array('PS_SIGNATURE' => $Sig);
    }


    function getSignature ($queryString=false)
    {
        if (empty($this->secret)){
            throw new exception ("Paramsigner secret is empty!");
        }
        $this->setParam('PS_EXPIRETIME',time()+(3600*$this->lifetime));
        $this->setParam('PS_SIGTYPE',$this->signatureType);
        $sigstring=$this->secret;
        $urlencstring='';
        ksort($this->params,SORT_STRING);
        foreach ($this->params as $key=>$value){
            $sigstring.="&".$key.'='.$value;
            $urlencstring.="&".urlencode($key).'='.urlencode($value);
        }
        switch ($this->params['PS_SIGTYPE']){
            case 'md5':
            case 'MD5':
            case 'PSMD5':
                $signature=md5($sigstring);
                break;
            case 'PSSHA1':
            case 'sha1':
            case 'SHA1':
                $signature=sha1($sigstring);
                break;
            default:
                throw new exception('Unknown key signatureType');
        }

        if ($queryString){
            return 'PS_SIGNATURE='.urlencode($signature).$urlencstring;
        }else{
            return $signature;
        }
    }

    private function _checkSignatureType($value)
    {
        if ($value=='md5') return true;
        if ($value=='MD5') return true;
        if ($value=='PSMD5') return true;
        if ($value=='sha1') return true;
        if ($value=='SHA1') return true;
        if ($value=='PSSHA1') return true;
        return false;
    }

    public static function paramAuthenticate ($paramArray, $secret = FALSE)
    {
        $sentSignature = @$paramArray['PS_SIGNATURE'];
        unset($paramArray['PS_SIGNATURE']);
        $string = '';
        ksort($paramArray, SORT_STRING);
        foreach ($paramArray as $key => $value) {
            $string.="&".$key.'='.$value;
        }
        switch (@$paramArray['PS_SIGTYPE']) {
            case 'MD5':
            case 'md5':
            case 'PSMD5':
                $signature = md5($secret . $string);
                break;
            case 'sha1':
            case 'SHA1':
            case 'PSSHA1':
                $signature = sha1($secret . $string);
                break;
            default:
                return false;
        }
        if ($sentSignature != $signature) {
            return false;
        }

        unset($paramArray['PS_SIGTYPE']);
        unset($paramArray['PS_EXPIRETIME']);
        return $paramArray;
    }
}
