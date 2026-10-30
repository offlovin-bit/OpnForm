<?php

namespace App\Mail;

use App\Models\UserInvite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserInvitationEmail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;


    /**
     * Create a new message instance.
     *
     * @param string $workspaceName
     * @return void
     */
    public function __construct(public UserInvite $invite)
    {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // OFFLOVIN: invitación propia en español (mail.user.invitacion) en lugar de la de OpnForm
        $workspace = $this->invite->workspace;
        $icon = $workspace->icon;
        $datos = [
            'workspaceName' => $workspace->name,
            'workspaceIcon' => (is_string($icon) && str_starts_with($icon, 'https://')) ? $icon : null,
            'inviteLink' => $this->invite->getLink(),
            'caduca' => \Carbon\Carbon::parse($this->invite->valid_until)->locale('es')->isoFormat('D [de] MMMM'),
        ];
        return $this
            ->view('mail.user.invitacion', $datos)
            ->text('mail.user.invitacion-texto', $datos)
            ->subject('Te han invitado a ' . $workspace->name . ' en Formularios');
    }
}
