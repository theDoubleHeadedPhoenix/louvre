<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as BillAssert;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Ticket;
/**
 * Bill
 *
 * @ORM\Table(name="bill")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BillRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Bill
{
    const TYPE_ALL_JOURNEY = "allJourney";
    const TYPE_HALF_JOURNEY = "halfJourney";
    const TYPE_TICKET_TYPE_ARRAY = [
        'form.billStep1.allJourney'   => self::TYPE_ALL_JOURNEY,
        'form.billStep1.halfJourney'   => self::TYPE_HALF_JOURNEY
    ];
    const HOUR_APPLY_HALF_JOURNEY_AFTER = 14;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100)
     * @Assert\NotBlank
     * @Assert\Email(  
     *  checkMX = true   
     * )
     
     */
    private $email;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_of_booking", type="datetime")
     * @Assert\NotBlank
     * @Assert\DateTime
     * @Assert\GreaterThanOrEqual("today")
     * @Assert\LessThan("today +1 years")
     * @BillAssert\ValidDateOfBooking(message="bill.dateOfBooking.validDateOfBooking")
     */
    private $dateOfBooking;

    /**
     * @var string
     *
     * @ORM\Column(name="ticket_type", type="string", length=12)
     * @Assert\NotBlank
     * @Assert\Choice(
     *  choices = {Bill::TYPE_ALL_JOURNEY, Bill::TYPE_HALF_JOURNEY},
     *  strict = true
     * )
     * @BillAssert\ValidTicketType(message="bill.ticketType.validTicketType")
     */
    private $ticketType;

    /**
     * @var string
     *
     * @ORM\Column(name="stripe_id", type="string")
     */
    private $stripeId;

    /**
     * @var int
     *
     * @ORM\Column(name="number_of_tickets", type="integer")
     * @Assert\GreaterThanOrEqual(1)
     * @Assert\LessThanOrEqual(1000)
     * @Assert\NotBlank
     */
    private $numberOfTickets = 1;

    /**
     * @var int
     *
     * @ORM\Column(name="total_price", type="integer")
     */
    private $totalPrice;

    /**
    * @var ArrayCollection
    * @ORM\OneToMany(targetEntity="Ticket", mappedBy="bill", orphanRemoval=true, cascade="persist")
    * @Assert\All({
    *   @Assert\Type(type="AppBundle\Entity\Ticket")
    * })
    * @Assert\Valid
    */
    private $tickets;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
        $this->dateOfBooking = new \DateTime();
    }
    function __clone() {
        $this->tickets = clone $this->tickets;
    }
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Bill
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Bill
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set dateOfBooking
     *
     * @param \DateTime $dateOfBooking
     *
     * @return Bill
     */
    public function setDateOfBooking($dateOfBooking)
    {
        $this->dateOfBooking = new \DateTime($dateOfBooking);

        return $this;
    }

    /**
     * Get dateOfBooking
     *
     * @return \String
     */
    public function getDateOfBooking()
    {
        return $this->dateOfBooking->format('d/m/Y');
    }

    /**
     * Set ticketType
     *
     * @param string $ticketType
     *
     * @return Bill
     */
    public function setTicketType($ticketType)
    {
        $this->ticketType = $ticketType;

        return $this;
    }

    /**
     * Get ticketType
     *
     * @return string
     */
    public function getTicketType()
    {
        return $this->ticketType;
    }
    /**
     * Set tickets
     *
     * @param string $tickets
     *
     * @return Bill
     */
    public function setTickets($tickets)
    {
        //Si array créer un arraycollection de tickets
        if(is_array($tickets)){
            $this->tickets = new ArrayCollection();            
            foreach($tickets as $ticket){
                $newTicket = new Ticket();
                $newTicket->setFirstname($ticket['firstname']);
                $this->tickets->add($newTicket);
            }
            return $this;
        }
        $this->tickets = $tickets;

        return $this;
    }

    /**
     * Get tickets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTickets()
    {
        return $this->tickets;
    }
    /**
     * Set numberOfTickets
     *
     * @param string $numberOfTickets
     *
     * @return Bill
     */
    public function setNumberOfTickets($numberOfTickets)
    {
        $this->numberOfTickets = $numberOfTickets;

        return $this;
    }

    /**
     * Get numberOfTickets
     *
     * @return string
     */
    public function getNumberOfTickets()
    {
        return $this->numberOfTickets;
    }

    /**
     * Get stripeId
     *
     * @return string
     */
    public function getStripeId()
    {
        return $this->stripeId;
    }

    /**
     * Set stripeId
     *
     * @param int $stripeId
     *
     * @return Bill
     */
    public function setStripeId($stripeId)
    {
        $this->stripeId = $stripeId;

        return $this;
    }

    /**
     * Set totalPrice
     *
     * @param integer $totalPrice
     *
     * @return Bill
     */
    public function setTotalPrice($totalPrice)
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    /**
     * Get totalPrice
     *
     * @return int
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
    }
    public function removeTicket($key){
        $this->tickets->remove($key);
        $this->setNumberOfTickets($this->getNumberOfTickets()-1);
        return $this;
    }
    /**
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->setCreatedAt(new \DateTime("now"));
    }

    /**
     * Add ticket
     *
     * @param \AppBundle\Entity\Ticket $ticket
     *
     * @return Bill
     */
    public function addTicket(\AppBundle\Entity\Ticket $ticket)
    {
        $this->tickets[] = $ticket;

        return $this;
    }
    /**
     * Compte les tickets dans bill
     * @return int
     */
    public function countTickets(){
        return count($this->tickets);
    }
}
