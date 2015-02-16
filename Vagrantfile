Vagrant.require_version ">= 1.7.2"

Vagrant.configure("2") do |config|

  config.vm.box = 'arch64'
  config.vm.box_url = 'http://cloud.terry.im/vagrant/archlinux-x86_64.box'

  config.vm.network :private_network, ip: "1.3.3.7"

  config.vm.provider :virtualbox do |vb|
    vb.customize ["modifyvm", :id, "--memory", 512]
    vb.customize ["modifyvm", :id, "--cpus", 1]
  end

  config.vm.synced_folder '.', '/home/vmail'
  config.vm.synced_folder 'www', '/var/www', id: 'vagrant-root',
    owner: 'http',
    group: 'http',
    mount_options: ['dmode=755,fmode=755']
  config.vm.synced_folder 'www/app/cache', '/var/www/app/cache', mount_options: ['dmode=777,fmode=777']
  config.vm.synced_folder 'www/app/logs', '/var/www/app/logs', mount_options: ['dmode=777,fmode=777']

  config.vm.provision :shell, :path => 'bootstrap.sh'
end
