<?php

namespace Resova\Endpoints;

use Resova\Client;
use Resova\Models\Webhook;
use Resova\Models\WebhookDelete;
use Resova\Models\WebhookList;

class Webhooks extends Client
{
    /**
     * @var string
     */
    protected $namespace = __CLASS__;

    /**
     * @param int $webhook_id The webhook id
     *
     * @return $this
     */
    public function __invoke(int $webhook_id): self
    {
        $this->webhook_id = $webhook_id;

        return $this;
    }

    /**
     * List all webhooks
     * Returns a list of your promotions. The promotions are returned sorted by creation date, with the most recent promotion appearing first.
     *
     * @return $this
     */
    public function all(): self
    {
        // Set HTTP params
        $this->type     = 'get';
        $this->endpoint = '/webhooks';
        $this->response = WebhookList::class;

        return $this;
    }

    /**
     * Create a webhook
     *
     * @param Webhook $webhook
     *
     * @return $this
     */
    public function create(Webhook $webhook): self
    {
        $webhook->setRequired([
            'endpoint',
            'events',
        ]);

        // Set HTTP params
        $this->type     = 'post';
        $this->endpoint = '/webhooks';
        $this->params   = $webhook;
        $this->response = Webhook::class;

        return $this;
    }

    /**
     * Update a webhook
     *
     * @param Webhook $webhook
     *
     * @return $this
     */
    public function update(Webhook $webhook): self
    {
        $webhook->setRequired([
            'endpoint',
            'events',
        ]);

        // Set HTTP params
        $this->type     = 'put';
        $this->endpoint = '/webhooks/' . $this->webhook_id;
        $this->params   = $webhook;
        $this->response = Webhook::class;

        return $this;
    }

    /**
     * Delete a webhook
     *
     * @return $this
     */
    public function delete(): self
    {
        // Set HTTP params
        $this->type     = 'delete';
        $this->endpoint = '/webhooks/' . $this->webhook_id;
        $this->response = WebhookDelete::class;

        return $this;
    }
}
