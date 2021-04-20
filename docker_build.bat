docker build -t rest_2020 . --no-cache 
docker run -dit -p 8084:8080 --name rest_2020 -v C:/CETAapps/REST:/var/www/html --env-file C:/CETAapps/REST/dev_env.list rest_2020
