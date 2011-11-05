#!/bin/bash

git pull origin master
cd /etc/tinydns/root
sudo make
