FROM ruby

ADD . /webapp

WORKDIR /webapp

RUN apt-get update && \
    apt-get install -yy libsqlite3-dev && \
    bundle install --without=development test && \
    rake assets:precompile && \
    apt-get autoclean && apt-get autoremove

CMD rails s -e production -b 0.0.0.0 -p 80
