<?php

interface msDeliveryInterface
{

    /**
     * Returns an additional cost depending on the method of delivery
     *
     * @param msOrderInterface $order
     * @param msDelivery $delivery
     * @param float $cost
     *
     * @return float|integer
     */
    public function getCost(msOrderInterface $order, msDelivery $delivery, $cost = 0.0);


    /**
     * Returns delivery time depending on the method of delivery
     *
     * @param msOrderInterface $order
     * @param msDelivery $delivery
     *
     * @return string
     */
    public function getTime(msOrderInterface $order, msDelivery $delivery);


    /**
     * Returns failure response
     *
     * @param string $message
     * @param array $data
     * @param array $placeholders
     *
     * @return array|string
     */
    public function error($message = '', $data = array(), $placeholders = array());


    /**
     * Returns success response
     *
     * @param string $message
     * @param array $data
     * @param array $placeholders
     *
     * @return array|string
     */
    public function success($message = '', $data = array(), $placeholders = array());
}


class msDeliveryHandler implements msDeliveryInterface
{
    /** @var modX $modx */
    public $modx;
    /** @var miniShop2 $ms2 */
    public $ms2;


    /**
     * @param xPDOObject $object
     * @param array $config
     */
    function __construct(xPDOObject $object, $config = array())
    {
        $this->modx = $object->xpdo;
        $this->ms2 = $object->xpdo->getService('miniShop2');
    }


    /**
     * @param msOrderInterface $order
     * @param msDelivery $delivery
     * @param float $cost
     *
     * @return float|int
     */
    public function getCost(msOrderInterface $order, msDelivery $delivery, $cost = 0.0)
    {
        if (empty($this->ms2)) {
            $this->ms2 = $this->modx->getService('miniShop2');
        }
        if (empty($this->ms2->cart)) {
            $this->ms2->loadServices($this->ms2->config['ctx']);
        }

        $cart = $this->ms2->cart->status();
        $hash = 'ms2delivery/' . md5(json_encode(array(
            $delivery->get('id'),
            $cart['total_cost'],
            $cart['total_weight'],
        )));

        $cacheTime = $delivery->get('cache_time');
        if ($cacheTime > 0){
            if ($cache = $this->modx->cacheManager->get($hash)){
                return $cache;
            }
        }

        $weight_price = $delivery->get('weight_price');
        //$distance_price = $delivery->get('distance_price');

        $cart_weight = $cart['total_weight'];
        $cost += $weight_price * $cart_weight;

        $add_price = $delivery->get('price');
        if (preg_match('/%$/', $add_price)) {
            $add_price = str_replace('%', '', $add_price);
            $add_price = $cost / 100 * $add_price;
        }
        $cost += $add_price;

        if ($cacheTime > 0){
            $this->modx->cacheManager->set($hash, $cost, $cacheTime * 60);
        }

        return $cost;
    }


    /**
     * @param msOrderInterface $order
     * @param msDelivery $delivery
     *
     * @return string
     */
    public function getTime(msOrderInterface $order, msDelivery $delivery)
    {
        return $delivery->get('time');
    }


    /**
     * @param string $message
     * @param array $data
     * @param array $placeholders
     *
     * @return array|string
     */
    public function error($message = '', $data = array(), $placeholders = array())
    {
        if (empty($this->ms2)) {
            $this->ms2 = $this->modx->getService('miniShop2');
        }

        return $this->ms2->error($message, $data, $placeholders);
    }


    /**
     * @param string $message
     * @param array $data
     * @param array $placeholders
     *
     * @return array|string
     */
    public function success($message = '', $data = array(), $placeholders = array())
    {
        if (empty($this->ms2)) {
            $this->ms2 = $this->modx->getService('miniShop2');
        }

        return $this->ms2->success($message, $data, $placeholders);
    }

}