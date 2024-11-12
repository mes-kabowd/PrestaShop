# Define the version of the base image to use
ARG VERSION

# Use the specified PrestaShop base image version
FROM prestashop/prestashop:$VERSION

# Expose port 443 for HTTPS
EXPOSE 443

# Define arguments for group ID, user ID, and Node.js version
ARG GROUP_ID
ARG USER_ID
ARG NODE_VERSION

# Modify the group and user IDs for www-data to match the specified IDs
RUN groupmod -g $GROUP_ID www-data \
  && usermod -u $USER_ID -g $GROUP_ID www-data

# Copy helper scripts to the /tmp directory in the container
COPY .docker/wait-for-it.sh /tmp/
COPY .docker/docker_run_git.sh /tmp/

# Create and set permissions for the .npm directory
RUN mkdir -p /var/www/.npm
RUN chown -R www-data:www-data /var/www/.npm

# Create and set permissions for the .composer directory
RUN mkdir -p /var/www/.composer
RUN chown -R www-data:www-data /var/www/.composer
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Define environment variable for NVM (Node Version Manager) directory
ENV NVM_DIR /usr/local/nvm

# Install NVM, Node.js, and set the default Node.js version
RUN mkdir -p $NVM_DIR \
    && curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash \
    && . $NVM_DIR/nvm.sh \
    && nvm install $NODE_VERSION \
    && nvm alias default $NODE_VERSION \
    && nvm use default

# Define environment variables for Node.js path
ENV NODE_PATH $NVM_DIR/versions/node/v$NODE_VERSION/bin
ENV PATH $PATH:$NODE_PATH

# Install mailutils to enable sendmail functionality
RUN apt update && apt install -y mailutils

# Install Composer globally
RUN php -r "copy('https://getcomposer.org/installer', '/tmp/composer-setup.php');" \
    && php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer \
    && rm -rf /tmp/composer-setup.php

# Set the default command to run the docker_run_git.sh script
CMD ["/tmp/docker_run_git.sh"]