<?php

namespace Resova\Endpoints\Baskets;

use Resova\Client;
use Resova\Requests\Booking;

class Bookings extends Client
{
    /**
     * Create a basket
     * Creates a new basket object.
     *
     * @param Booking $booking
     *
     * @return $this
     */
    public function create(Booking $booking): self
    {
        $booking->setRequired([
            'instance_id',
            'quantities'
        ]);

        // Set HTTP params
        $this->type     = 'post';
        $this->endpoint = $this->config->get('base_uri') . '/baskets/' . $this->basket_id . '/bookings';
        $this->params   = $booking;

        return $this;
    }

    /**
     * Retrieve a basket booking
     * Retrieves the details of a basket booking. Provide the unique id for the basket booking.
     *
     * @param int $booking_id The basket booking id
     *
     * @return $this
     */
    public function __invoke(int $booking_id): self
    {
        $this->booking_id = $booking_id;

        // Set HTTP params
        $this->type     = 'get';
        $this->endpoint = $this->config->get('base_uri') . '/baskets/' . $this->basket_id . '/bookings/' . $booking_id;

        return $this;
    }

    /**
     * Delete a basket booking
     * Permanently deletes a basket booking. It cannot be undone.
     *
     * @return $this
     */
    public function delete(): self
    {
        // Set HTTP params
        $this->type     = 'delete';
        $this->endpoint = $this->config->get('base_uri') . '/baskets/' . $this->basket_id . '/bookings/' . $this->booking_id;

        return $this;
    }

    /**
     * Update a basket booking
     * Updates the specified basket booking by setting the values of the parameters passed.
     * Any parameters not provided will be left unchanged.
     * This request accepts mostly the same arguments as the basket booking creation call.
     *
     * @param null|Booking $booking
     *
     * @return $this
     */
    public function update(Booking $booking = null): self
    {
        // Set HTTP params
        $this->type     = 'put';
        $this->endpoint = $this->config->get('base_uri') . '/baskets/' . $this->basket_id . '/bookings';
        $this->params   = $booking;

        return $this;
    }

    /**
     * List basket bookings
     * Returns a list of your basket bookings.
     * The basket bookings are returned sorted by creation date, with the most recent basket booking appearing first.
     *
     * @return $this
     */
    public function all(): self
    {
        // Set HTTP params
        $this->type     = 'get';
        $this->endpoint = $this->config->get('base_uri') . '/baskets/' . $this->basket_id . '/bookings';

        return $this;
    }
}
