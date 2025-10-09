
# Notebooks APP

Gestão de conteúdos, Tópicos e questões para serem respondidas em CADERNOS DE QUESTÕES, com estátisticas, reports e resolução.




## Installation

```Storage
  🔗 Configuração Manual do Link de Storage (Para Ambientes Onde php artisan storage:link Não Funciona)
  📍 Passos para criar o link manualmente:
    Acesse o terminal/SSH do servidor.

    Identifique o caminho raiz do projeto Laravel.
    Exemplo típico:
    /home/SEU_USUARIO/domains/SEU_DOMINIO/public_html/SEU_SUBDIRETORIO
    Remova o diretório public/storage se já existir:

    Remova o diretório public/storage se já existir:
    rm -rf public/storage
    Crie o link simbólico:

    Crie o link simbólico:
    ln -s /home/SEU_USUARIO/domains/SEU_DOMINIO/public_html/SEU_SUBDIRETORIO/storage/app/public /home/SEU_USUARIO/domains/SEU_DOMINIO/public_html/SEU_SUBDIRETORIO/public/storage
    
    Exemplo real:
    ln -s /home/xxxxx/domains/foconadivisa.com.br/public_html/demo/storage/app/public /home/xxxxxx/domains/

    Observações:
    Você pode usar o comando pwd para localizar o endereço/caminho
```
    
## Authors

- [@thiagoprogramando](https://github.com/thiagoprogramando)

