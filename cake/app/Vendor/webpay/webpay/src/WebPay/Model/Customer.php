<?php

namespace WebPay\Model;

class Customer extends Entity
{
    /** var bool */
    private $updateEmail = false;

    /** var bool */
    private $updateDescription = false;

    /** var bool */
    private $updateCard = false;

    /** var array */
    private $newCard = null;

    public function __construct(\WebPay\WebPay $client, array $data)
    {
        if (array_key_exists('active_card', $data) && !empty($data['active_card'])) {
            $data['active_card'] = new Card($data['active_card']);
        }
        parent::__construct($client, $data);
    }

    /**
     * Classify this response object from DeletedEntity
     *
     * @return false
     */
    public function isDeleted()
    {
        return false;
    }

    /**
     * Set new email address for save
     *
     * @param string $email The new email address
     */
    public function setEmail($email)
    {
        $this->data['email'] = $email;
        $this->updateEmail = true;
    }

    /**
     * Set new description address for save
     *
     * @param string $description The new description address
     */
    public function setDescription($description)
    {
        $this->data['description'] = $description;
        $this->updateDescription = true;
    }

    /**
     * Set new card parameters for save
     *
     * @param mixed $card The new card parameters
     */
    public function setNewCard($card)
    {
        $this->newCard = $card;
        $this->updateCard = true;
    }

    /**
     * Save this charge
     *
     * This method updates only changed fields.
     *
     * @return self
     */
    public function save()
    {
        $params = array();
        if ($this->updateEmail)
            $params['email'] = $this->email;
        if ($this->updateDescription)
            $params['description'] = $this->description;
        if ($this->updateCard)
            $params['card'] = $this->newCard;

        $this->data = $this->client->customers->save($this->id, $params)->data;
        $this->clearAfterSave();

        return $this;
    }

    private function clearAfterSave()
    {
        $this->newCard = null;
        $this->updateEmail = false;
        $this->updateDescription = false;
        $this->updateCard = false;
    }

    /**
     * Delete this charge
     *
     * @return bool True if succeeded
     */
    public function delete()
    {
        return $this->client->customers->delete($this->id);
    }
}
