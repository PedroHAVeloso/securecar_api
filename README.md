# SecureCar REST API

## API do TCC SecureCar.
- *Está em versão de teste*.
- *v0.1.3-alpha*.


> ## Funcionalidades atuais:
>- Cadastro de usuário;
>- Envio do código verificador por e-mail;
>- Validação de usuário; 
>- Login de usuário;
>- Verificação de sessão;
>- Fechamento de sessão.  

> ## Ainda a implementar:
>- Filtragem e validação dos dados recebidos pela API;
>- Melhoria no tratamento de erros;
>- Modificações de dados do usuário;
>- Exclusão de usuário;
>- Demais rotas de acordo com o aplicativo SecureCar (ainda privado).

# Rodando a API
As variáveis de ambiente estão contidas no `.env.example`. Serão responsáveis pela garantia da conexão com o banco de dados. Crie um novo arquivo `.env` e adicione-as.

O banco de dados usado pela API está em `db_securecar.sql`.

## Com o servidor web embutido do PHP

	$ php -S localhost:5050

# REST API

Descrição das funcionalidades da API.

### Url
		
	http://localhost:5050/securecar_api/

### Autorização

Todas as Requests devem conter em seu `header`:

	Authorization: api_key

A `api_key` é uma chave de acesso armazenada no banco de dados. 
	
## Cadastrar usuário

#### Request 

`POST /user/register`

	{
		"name": string,
		"email": string,
		"cpf": string,
		"birth": string,
		"password": string,
		"validation_code": int
	}
	
#### Response OK

	{
		"status": "OK",
		"register": true,
		"session_token": "token"
	}
	
## Validação de usuário

#### Request

`PUT /user/validate`

	{
		"email": string,
		"validation_code": int
	}
	
#### Response OK
	
	{
		"status": "OK",
		"validate": true
	}
	
##  Login de usuário

#### Request

`POST /user/login`

	{
		"email": string,
		"password": string
	}

#### Response OK

	{
		"status": "OK",
		"login": true
		"session_token": "token",
		"user": {
			"name": "name",
			"birth": "yyyy-MM-dd",
			"cpf": "10010010010",
			"is_validated": 2
		}
	}

## Verificação de sessão

#### Request

`POST /user/check-session-validity`

	{ 
		"session_token": string
	}

#### Response OK

	{
		"status": "OK",
		"valid": true
	}

## Fechamento de sessão

#### Request

`DELETE /user/close-session`

	{
		"session_token": "token"
	}

#### Response OK

	{
		"status": "OK,
		"closed": true
	}

## Envio do código verificador por e-mail

#### Request

`POST /email/send-user-code`

	{
		"email": "email",
		"name": "name",
		"code": 1111
	}

#### Response OK

	{
		"status": "OK
	}

