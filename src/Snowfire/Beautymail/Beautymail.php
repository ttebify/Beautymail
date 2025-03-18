<?php

namespace Snowfire\Beautymail;

use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Mail\PendingMail;
use Illuminate\Support\Facades\Request;
use Illuminate\Mail\SentMessage;

class Beautymail implements Mailer
{
    /**
     * Contains settings for emails processed by Beautymail.
     *
     * @var array
     */
    private $settings;

    /**
     * The mailer contract depended upon.
     *
     * @var \Illuminate\Contracts\Mail\Mailer
     */
    private $mailer;

    /**
     * Initialise the settings and mailer.
     *
     * @param array $settings
     */
    public function __construct($settings)
    {
        $this->settings = $settings;
        $this->mailer = app()->make(Mailer::class);
        $this->setLogoPath();
    }

    public function to($users)
    {
        return (new PendingMail($this))->to($users);
    }

    public function bcc($users)
    {
        return (new PendingMail($this))->bcc($users);
    }
    
    public function cc($users)
    {
        return (new PendingMail($this))->cc($users);
    }

    /**
     * Retrieve the settings.
     *
     * @return array
     */
    public function getData()
    {
        return $this->settings;
    }
    
    /**
     * @return \Illuminate\Contracts\Mail\Mailer
     */
    public function getMailer()
    {
        return $this->mailer;
    }

    /**
     * Send a new message using a view.
     *
     * @param string|array    $view
     * @param array           $data
     * @param \Closure|string $callback
     *
     * @return void
     */
    public function send($view, array $data = [], $callback = null)
    {
        $data = array_merge($this->settings, $data);

        $this->mailer->send($view, $data, $callback);
    }

    /**
     * Send a new message synchronously using a view.
     *
     * @param  \Illuminate\Contracts\Mail\Mailable|string|array  $mailable
     * @param  array  $data
     * @param  \Closure|string|null  $callback
     * @return \Illuminate\Mail\SentMessage|null
     */
    public function sendNow($mailable, array $data = [], $callback = null)
    {
        $data = array_merge($this->settings, $data);

        return $this->mailer->sendNow($mailable, $data, $callback);
    }

    /**
     * Send a new message using the a view via queue.
     *
     * @param string|array    $view
     * @param array           $data
     * @param \Closure|string $callback
     *
     * @return mixed
     */
    public function queue($view, array $data, $callback)
    {
        $data = array_merge($this->settings, $data);

        return $this->mailer->queue($view, $data, $callback);
    }

    /**
     * @param $view
     * @param array $data
     *
     * @return \Illuminate\View\View
     */
    public function view($view, array $data = [])
    {
        $data = array_merge($this->settings, $data);

        return view($view, $data);
    }

    /**
     * Send a new message when only a raw text part.
     *
     * @param string $text
     * @param mixed  $callback
     *
     * @return void
     */
    public function raw($text, $callback)
    {
        return $this->mailer->send(['raw' => $text], [], $callback);
    }

    /**
     * Get the array of failed recipients.
     *
     * @return array
     */
    public function failures()
    {
        return $this->mailer->failures();
    }

    /**
     * @return void
     */
    private function setLogoPath()
    {
        $this->settings['logo']['path'] = str_replace(
            '%PUBLIC%',
            Request::getSchemeAndHttpHost(),
            $this->settings['logo']['path']
        );
    }
}
