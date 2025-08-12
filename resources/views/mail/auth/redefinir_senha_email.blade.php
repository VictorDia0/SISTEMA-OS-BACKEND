@extends('mail.sistema.mail_sistema')

@section('mensagem')
    <p>Olá, <strong>{{ $notifiable->name }}</strong></p>
    <p>Para redefinir sua senha e recuperar o acesso à sua conta, por favor, clique no botão abaixo:</p>

    <div class="center-text">
        <a href="{{ $url }}" class="button-link" style="text-transform: uppercase">
            Redefinir senha
        </a>
    </div>

    <div class="center-text" style="text-align: center;">
        <p style="color:#aaa; font-size: 10px;">Se o botão acima não funcionar, copie e cole o link abaixo no seu navegador: <br> <a href="{{$url}}">{{$url}}</a></p>

        <p style="color:#bf2000; font-size: 10px;">O link possui validade de 24 horas. Após esse período, ele estará indisponível.</p>
    </div>
    <p>Caso você não tenha esquecido sua senha, desconsidere este email.</p>
    <p>Atenciosamente,</p>
    <p>Equipe Pharus</p>
@endsection
