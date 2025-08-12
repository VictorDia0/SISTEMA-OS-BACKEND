@extends('mail.sistema.mail_sistema')

@section('mensagem')
<p>Olá, <strong>{{ $notifiable->name }}</strong></p>
<p>Sua senha foi alterada com sucesso!</p>

<div class="center-text">
    <a style="text-transform: uppercase" href="{{ env('FRONTEND_URL') }}" class="button-link">
        Acessar o sistema
    </a>
</div>

<p style="color:#bf2000">Caso você não tenha alterado sua senha, entre em contato com o <a href="mailto:{{env('MAIL_FROM_ADDRESS')}}?subject=Notificação de alteração de senha não autorizada&cc={{$notifiable->email}}">suporte</a>.</p>
<p>Atenciosamente,</p>
<p>Equipe Pharus</p>
@endsection