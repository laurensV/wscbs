apt-get update
apt-get install -y python-dev
apt-get install -y pip
apt-get install -y python-numpy
apt-get install -y python-scipy
apt-get install -y git
apt-get install -y tmux

# Install pumpkin & its dependencies
git clone https://github.com/recap/pumpkin.git /home/ubuntu/pumpkin
cd /home/ubuntu/pumpkin && git checkout tracula
pip install ujson
apt-get install -y python-zmq
pip install pyinotify
pip install tftpy
pip install networkx
pip install pika
pip install netaddr
pip install pyftpdlib
pip install nltk
cd /home/ubuntu/pumpkin && python setup.py install

# Clone course git for easy access
git clone https://github.com/SOA-cloud-course/sentiment-analysis-pumpkin.git /home/ubuntu/sentiment-analysis-pumpkin

# Copy config
cp /mnt/pumpkin.cfg /home/ubuntu/pumpkin/pumpkin.cfg

# Copy worker seeds
cp /mnt/*.py /home/ubuntu/pmk-seeds/
