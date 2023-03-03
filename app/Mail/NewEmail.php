<?php

namespace App\Mail;
// use App\Http\Controllers\Taxi\Web\Email\EmailController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $content;
    // public $settings;
    // public $pdf;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject,$content)
    {
        $this->subject = $subject;
        $this->content = $content;
        // $this->settings = $settings;
        // $this->pdf = $pdf;
    }
    public function replaceContent() {

        $this->subject = str_replace('{{$content,$subject}}',$this->content,$this->subject);
        // $this->content = str_replace('{{$subject}}', $this->content);
        
    }
    
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->replaceContent();
        return $this->view('taxi.email.mailtemp')->with(['content' => $this->content,'subject' => $this->subject]);
    }
}
