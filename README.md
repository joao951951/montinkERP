# 🛍️ Desafio Laravel

[![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?logo=php)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?logo=mysql)](https://mysql.com)

Sistema ERP com:
- Carrinho de compras
- Cálculo de frete via ViaCEP
- Cadastro de produtos e suas variações

## 🚀 Começando

### Pré-requisitos
- PHP 8.1+
- Composer 2.5+
- MySQL ou SQLite
- Git

### Instalação

1. Clone o repositório:
```bash
git clone https://github.com/joao951951/montinkERP.git
cd ecommerce-laravel
```
2. Instale as dependências PHP

```bash
composer install
```
3. Configure o ambiente

```bash
cp .env.example .env
php artisan key:generate
```
4. Configure o arquivo .env

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nome_do_banco
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```
5. Inicie o servidor

```bash
php artisan serve
```

6. Tudo certo agora a aplicação está disponível basta acessar

```
http://localhost:8000
```