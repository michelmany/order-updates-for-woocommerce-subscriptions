<?php
/**
 * Plugin Name:  Order Updates for WooCommerce Subscriptions
 * Description:  Order Updates for WooCommerce Subscriptions allows you to update the Order Items.
 * Version:      1.0.0
 * Author:       Michel Moraes
 * Author URI:   https://www.codeable.io/developers/michel-moraes-nitdesign/
 * License:      MIT License
 */

class NIT_OUWCS {

    public function init(): void
    {
        add_filter( 'wcs_renewal_order_created', array( $this, 'renewal_order_created' ), -1000, 2 );
        add_action( 'woocommerce_checkout_create_order', array( $this, 'wc_checkout_create_order' ), 10, 2 );
    }

    /**
     * @param $order
     * @param $subscription
     * @return object
     */
    public function renewal_order_created( $order, $subscription )
    {
        $get_paid_count = $subscription->get_payment_count( 'completed', array( 'parent', 'renewal' ) );
        $box_month_number = (int) $get_paid_count + 1;

        $this->update_order_item_data( $order, $box_month_number );

        return $order;
    }

    /**
     * @param $order
     * @return object
     */
    public function wc_checkout_create_order( $order )
    {
        $this->update_order_item_data( $order, 1 );

        return $order;
    }

    /**
     * @param $order
     * @param $box_month_number
     */
    private function update_order_item_data( $order, $box_month_number ): void
    {
        foreach ( $order->get_items() as $item ) {
            $item->set_name( $item->get_name() . " - BoxMonth" . $box_month_number );
            $item->update_meta_data( 'Type', "BoxMonth" . $box_month_number );
            $item->save();
        }
    }
}

$plugin = new NIT_OUWCS;
$plugin->init();


