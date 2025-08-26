<?php

namespace App\Mail;

use App\Models\Briefing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class PlanosProntosMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Briefing $briefing) {}

    /** Cabeçalhos / remetente / assunto */
    public function envelope(): Envelope
    {
        return new Envelope(
            from    : new Address('no-reply@contato.kivvo.app', 'Kivvo'),
            subject : 'Seus planos personalizados estão prontos!'
        );
    }

    /** View ou markdown e variáveis passadas */
    public function content(): Content
    {
        return new Content(
            markdown : 'emails.planos-prontos',
            with     : [
                'user'     => $this->briefing->user,
                'briefing' => $this->briefing,
            ],
        );
    }

    /** Anexos  */
    public function attachments(): array
    {
        return [
            Attachment::fromStorageDisk('local', $this->briefing->url_plano_alimentar)
                ->as('plano-alimentar.pdf')
                ->withMime('application/pdf'),

            Attachment::fromStorageDisk('local', $this->briefing->url_plano_treino)
                ->as('plano-treino.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
