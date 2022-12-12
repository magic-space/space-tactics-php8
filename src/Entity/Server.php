<?php

namespace App\Entity;

use App\Repository\ServerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServerRepository::class)]
class Server
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 125)]
    private ?string $game_name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $timezone = null;

    #[ORM\Column]
    private ?int $msg_delete_after = null;

    #[ORM\Column]
    private ?int $user_delete_after = null;

    #[ORM\Column]
    private ?int $inactive_delete_after = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $reminder_mail = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $activate_emails = null;

    #[ORM\Column(length: 75, nullable: true)]
    private ?string $sender_type = null;

    #[ORM\Column(length: 125)]
    private ?string $sender_mail = null;

    #[ORM\Column(length: 125, nullable: true)]
    private ?string $sendmailpath = null;

    #[ORM\Column(length: 125, nullable: true)]
    private ?string $smtp_host = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $smtp_ssl_tls = null;

    #[ORM\Column(nullable: true)]
    private ?int $smtp_port = null;

    #[ORM\Column(length: 125, nullable: true)]
    private ?string $smtp_username = null;

    #[ORM\Column(length: 125, nullable: true)]
    private ?string $smtp_password = null;

    #[ORM\Column(nullable: true)]
    private ?int $send_reminder_after = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGameName(): ?string
    {
        return $this->game_name;
    }

    public function setGameName(string $game_name): self
    {
        $this->game_name = $game_name;

        return $this;
    }

    public function getTimezone(): ?\DateTimeInterface
    {
        return $this->timezone;
    }

    public function setTimezone(?\DateTimeInterface $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getMsgDeleteAfter(): ?int
    {
        return $this->msg_delete_after;
    }

    public function setMsgDeleteAfter(int $msg_delete_after): self
    {
        $this->msg_delete_after = $msg_delete_after;

        return $this;
    }

    public function getUserDeleteAfter(): ?int
    {
        return $this->user_delete_after;
    }

    public function setUserDeleteAfter(int $user_delete_after): self
    {
        $this->user_delete_after = $user_delete_after;

        return $this;
    }

    public function getInactiveDeleteAfter(): ?int
    {
        return $this->inactive_delete_after;
    }

    public function setInactiveDeleteAfter(int $inactive_delete_after): self
    {
        $this->inactive_delete_after = $inactive_delete_after;

        return $this;
    }

    public function getReminderMail(): ?int
    {
        return $this->reminder_mail;
    }

    public function setReminderMail(int $reminder_mail): self
    {
        $this->reminder_mail = $reminder_mail;

        return $this;
    }

    public function getActivateEmails(): ?int
    {
        return $this->activate_emails;
    }

    public function setActivateEmails(int $activate_emails): self
    {
        $this->activate_emails = $activate_emails;

        return $this;
    }

    public function getSenderType(): ?string
    {
        return $this->sender_type;
    }

    public function setSenderType(?string $sender_type): self
    {
        $this->sender_type = $sender_type;

        return $this;
    }

    public function getSenderMail(): ?string
    {
        return $this->sender_mail;
    }

    public function setSenderMail(string $sender_mail): self
    {
        $this->sender_mail = $sender_mail;

        return $this;
    }

    public function getSendmailpath(): ?string
    {
        return $this->sendmailpath;
    }

    public function setSendmailpath(?string $sendmailpath): self
    {
        $this->sendmailpath = $sendmailpath;

        return $this;
    }

    public function getSmtpHost(): ?string
    {
        return $this->smtp_host;
    }

    public function setSmtpHost(?string $smtp_host): self
    {
        $this->smtp_host = $smtp_host;

        return $this;
    }

    public function getSmtpSslTls(): ?string
    {
        return $this->smtp_ssl_tls;
    }

    public function setSmtpSslTls(?string $smtp_ssl_tls): self
    {
        $this->smtp_ssl_tls = $smtp_ssl_tls;

        return $this;
    }

    public function getSmtpPort(): ?int
    {
        return $this->smtp_port;
    }

    public function setSmtpPort(?int $smtp_port): self
    {
        $this->smtp_port = $smtp_port;

        return $this;
    }

    public function getSmtpUsername(): ?string
    {
        return $this->smtp_username;
    }

    public function setSmtpUsername(?string $smtp_username): self
    {
        $this->smtp_username = $smtp_username;

        return $this;
    }

    public function getSmtpPassword(): ?string
    {
        return $this->smtp_password;
    }

    public function setSmtpPassword(?string $smtp_password): self
    {
        $this->smtp_password = $smtp_password;

        return $this;
    }

    public function getSendReminderAfter(): ?int
    {
        return $this->send_reminder_after;
    }

    public function setSendReminderAfter(?int $send_reminder_after): self
    {
        $this->send_reminder_after = $send_reminder_after;

        return $this;
    }
}
