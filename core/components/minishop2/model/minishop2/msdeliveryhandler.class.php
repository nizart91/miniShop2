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
     * @return float|integer|msDeliveryCostResult
     */
    public function getCost(msOrderInterface $order, msDelivery $delivery, $cost = 0.0);


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
    /** @var msDeliveryCostResult $result */
    public $result;


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
     * @return float|int|msDeliveryCostResult
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
                $cost,
                $cart['total_cost'],
                $cart['total_weight'],
            )));

        $cache = null;
        $cacheTime = $delivery->get('cache_time');
        if ($cacheTime > 0){
            $cache = $this->modx->cacheManager->get($hash);
        }

        if (!$cache) {
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

            $cache = array(
                'cost'      => $cost,
                'time'      => $delivery->get('time'),
            );

            if ($cacheTime > 0) {
                $this->modx->cacheManager->set($hash, $cache, $cacheTime * 60);
            }
        }

        return msDeliveryCostResult::getInstance($cache['cost'], $cache['time']);
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

class msDeliveryCostResult
{
    /**
     * Delivery cost
     *
     * @var float
     */
    public $cost = 0.0;


    /**
     * Time of delivery
     *
     * @var string
     */
    public $time = '';


    /**
     * Availability of delivery
     *
     * @var bool
     */
    public $available = true;


    /**
     * Error of delivery cost
     *
     * @var string
     */
    public $error = '';


    /**
     * Additional data
     *
     * @var array
     */
    public $additional = array();

    /**
     * @param float $cost
     * @param string $time
     * @param bool $available
     * @param string $error
     * @param array $additional
     *
     * @return msDeliveryCostResult
     */
    public static function getInstance (
        $cost = 0.0,
        $time = '',
        $available = true,
        $error = '',
        $additional = array()
    )
    {
        $model = new self();

        $model->cost        = $cost;
        $model->time        = $time;
        $model->available   = $available;
        $model->error       = $error;
        $model->additional  = $additional;

        return $model;
    }

    public function toArray()
    {
        $array = array();
        foreach (array_keys(get_class_vars(self::class)) as $key){
            $array[$key] = $this->{$key};
        }
        return $array;
    }
}