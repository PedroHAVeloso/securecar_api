# SecureCar REST API

### API do TCC SecureCar.
- *Está em versão de teste*.
- *v0.1.0-alpha*.

As variáveis de ambiente estão contidas no `.env.example`. 
O banco de dados da API está em `db_securecar.sql`.

> ## Funcionalidades atuais:
>- Cadastro de usuário;
>- Validação de usuário; 
>- Login de usuário;
>- Verificação de sessão;
>- Fechamento de sessão.  

> ## Ainda a implementar:
>- Filtragem dos dados recebidos pela API:
>-- Validação da data, códigos etc;
>- Melhoria no tratamento de erros;
>- Mudança de senha do usuário;
>- Exclusão de usuário;
>- Demais rotas e tabelas de acordo com o aplicativo SecureCar (ainda privado).

# REST API

Descrição das funcionalidades da API.

### Url
		
	http://localhost/securecar_api/

### Autorização

Todas as Requests devem conter em seu `header`:

	Authorization: api_key

A `api_key` é uma chave de acesso guardada no banco de dados da API. 
	
## Cadastrar usuário

#### Request 

`POST /user`

	{
		"name":  "Nome",
		"email":  "email@github.com",
		"cpf":  "50012304010",
		"birth":  "2000-10-10",
		"password":  "Senha",
		"validation_code":  1234
	}
	
#### Response OK

	{
		"status": 200,
		"register": true,
		"session_token": "token"
	}
	
## Validação de usuário

#### Request

`PUT /user`

	{
		"email": "email@github.com",
		"validation_code": 1234
	}
	
#### Response OK
	
	{
		"status": 200,
		"validate": true
	}
	
##  Login de usuário

#### Request

`POST /user`

	{
		"email": "email@github.com",
		"password": "Senha"
	}

#### Response OK

	{
		"status": 200,
		"login": true
	}

## Verificação de sessão

#### Request

`POST /user`

	{ 
		"session_token": "token"
	}

#### Response OK

	{
		"status": 200,
		"valid": true
	}

## Fechamento de sessão

#### Request

`DELETE /user`

	{
		"session_token": "token"
	}

#### Response OK

	{
		"status": 200,
		"closed": "YES"
	}
