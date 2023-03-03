<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MyTestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $request_detail;
    public $settings;
    public $pdf;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($request_detail,$settings,$pdf)
    {
        $this->request_detail = $request_detail;
        $this->settings = $settings;
        $this->pdf = $pdf;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Mail from Roda Taxi')->attachData($this->pdf->output(), $this->request_detail->request_number.'.pdf')
                    ->view('emails.RequestBillMail');
    }
}
