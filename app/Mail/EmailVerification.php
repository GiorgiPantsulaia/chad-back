<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailVerification extends Mailable
{
	use Queueable, SerializesModels;

	/**
	 * Create a new message instance.
	 *
	 * @return void
	 */
	public function __construct(array $data)
	{
		$this->email_data = $data;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build(): Mailable
	{
		return $this->from(env('MAIL_FROM_ADDRESS'), 'MovieQuotes')->subject('Verify Your Email')->view('mail.email-verification', ['email_data'=>$this->email_data]);
	}
}
