# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant::Config.run do |config|

  # We define one box (joindin), but
  config.vm.define :joindin do |ji_config|

    ji_config.vm.box = 'centos-62-64-puppet'
    ji_config.vm.box_url = 'http://packages.vstone.eu/vagrant-boxes/centos/6.2/centos-6.2-64bit-puppet-vbox.4.1.12.box'

    ji_config.vm.host_name = "joind.in"

    # Forward a port from the guest to the host, which allows for outside
    # computers to access the VM, whereas host only networking does not.
    ji_config.vm.forward_port 80, 8080

    # Share an additional folder to the guest VM. The first argument is
    # an identifier, the second is the path on the guest to mount the
    # folder, and the third is the path on the host to the actual folder.
    # config.vm.share_folder "v-data", "/vagrant_data", "../data"

    # Use :gui for showing a display for easy debugging of vagrant
    ji_config.vm.boot_mode = :gui

    ji_config.vm.provision :puppet do |puppet|
      puppet.manifests_path = "puppet/manifests"
      puppet.module_path = "puppet/modules"
      puppet.manifest_file = "joindin.pp"
      puppet.options = [
        '--verbose',
        # '--debug',
        # '--graph',
        # '--graphdir=/vagrant/puppet/graphs'
      ]
    end
  end
end
