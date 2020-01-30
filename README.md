# Laravel Assets

### Автоматическая генерация файлов стилей, скриптов и картинок
 
##### Установите NodeJS и Lessc:
 
~~~
RUN curl -sL https://deb.nodesource.com/setup_13.x | bash -
RUN apt-get install -y nodejs
RUN curl -L https://npmjs.org/install.sh | sh
RUN npm install -g less
RUN npm install -g clean-css
RUN npm install -g less-plugin-clean-css
~~~
