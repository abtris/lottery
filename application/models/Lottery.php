<?php
/**
 * Created by JetBrains PhpStorm.
 * User: prskavecl
 * Date: 6.11.10
 * Time: 13:30
 * To change this template use File | Settings | File Templates.
 */ 
class Application_Model_Lottery
{
    /**
     * Ticket store
     * @var array
     */
    private $registerTickets = array();
    /**
     * Last ticket id
     * @var int
     */
    private $lastTicket;

    private $path = '/tmp/lottery.store';
    /**
     * Storage
     */
    public function __construct()
    {

        if (file_exists($this->path)) {
            $content = file_get_contents($this->path);
            $this->registerTickets = unserialize($content);
        }
    }
    /**
     * Save 
     * @return void
     */
    public function save()
    {
        $content = serialize($this->registerTickets);
        file_put_contents($this->path, $content);
    }

    /**
     * @param SimpleXML $directMessage
     * @return bool
     */
    public function registerTicket($directMessage)
    {
        $ticket = array(
            'id' => (int) $directMessage->id,
            'sender_id' => (int) $directMessage->sender_id,
            'create_at' => (string) $directMessage->created_at,
            'text' => (string) $directMessage->text,
            'nick' => (string) $directMessage->sender_screen_name
        );
        if ($this->validateTicket($ticket)) {
            $this->lastTicket = $ticket['id'];
            $this->registerTickets[] = $ticket;
            $this->save();
            return true;
        }
        return false;
    }
    /**
     * Get All register tickets
     * @return array
     */
    public function getTickets()
    {
        return $this->registerTickets;
    }
    /**
     * Return ID last twitter message
     * @return int
     */
    public function getLastTicketId()
    {
        return (int) $this->lastTicket;
    }
    /**
     * Validate
     * @param array $ticket
     * @return bool
     */
    public function validateTicket($ticket)
    {
        return true;
    }

}
