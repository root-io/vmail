Vagrant.configure("2") do |config|

  config.vm.box = 'arch64'
  config.vm.box_url = 'http://cloud.terry.im/vagrant/archlinux-x86_64.box'

  config.vm.provider "virtualbox" do |v|
    v.customize ["modifyvm", :id, "--memory", 2048]
  end

  config.vm.network :private_network, ip: "1.3.3.7"

  config.vm.synced_folder '.', '/home/vmail'
  config.vm.provision :shell, :path => 'installer/install.sh'
end
