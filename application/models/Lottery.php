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
            $this->registerTickets = json_decode($content, true);
        }
    }
    /**
     * Save 
     * @return void
     */
    public function save()
    {
        $content = json_encode($this->registerTickets);
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
//        var_dump($this->validateTicket($ticket));
        if ($this->validateTicket($ticket)) {
            $this->lastTicket = $ticket['id'];
            $this->registerTickets['messages'][] = $ticket;
            try {
                $this->parseMessage($ticket['text']);
            } catch (Exception $e) {
                echo $e->getMessage();   
            }
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
        if (is_array($ticket)) {
            if (isset($this->registerTickets['messages'])) {
                foreach ($this->registerTickets['messages']  as $m) {
                    if ($m['id']==$ticket['id']) {
                        return false;
                    }
                }
            }
            return true;
        } 
    }
    /**
     * Parse
     * @param string $text
     * @return void
     */
    private function parseMessage($text)
    {
        preg_match('/(\d+)/', $text, $matches);
        // add check for existing
        if (!in_array($this->registerTickets['tickets'], $matches[0])) {
            $this->registerTickets['tickets'][] = $matches[0];
            return true;
        } else {
            throw new Exception('Ticket '.$matches[0].' will be register early');
            return false;
        }
         
        
        
    }

}
