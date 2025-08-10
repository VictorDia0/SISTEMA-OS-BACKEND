@extends('mail.sistema.mail_sistema')

@section('mensagem')
        <h1>Ativação de Conta!</h1>
        <br>
        <p>Olá, <strong>{{ $notifiable->name }}</strong></p>
        <p>Bem vindo ao Ordem de Serviço - Gestão de Ordem de Serviços! Para ativar sua conta e garantir a segurança, por favor, verifique seu endereço de email clicando no botão abaixo:</p>
        <div class="center-text">
            <a href="{{ $url }}" class="button-link">
                Verificar email
            </a>
        </div>
        <p style="font-size: 8px; text-align: center;">Se o botão acima não funcionar, copie e cole o link abaixo no seu navegador: <a href="{{$url}}">{{$url}}</a></p>
        <p>Se você não se cadastrou no nosso sistema, por favor ignore este email.</p>
        <br>
        <p>Atenciosamente,</p>
        <p><strong>Equipe Ordem de Serviço</strong></p>
        <p><a href="mailto:{{env('MAIL_FROM_ADDRESS')}}">{{env('MAIL_FROM_ADDRESS')}}</a></p>
@endsection
