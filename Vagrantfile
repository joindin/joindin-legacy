# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant::Config.run do |config|
  config.vm.box = 'puppet-centos-56-64'
  config.vm.box_url = 'http://puppetlabs.s3.amazonaws.com/pub/centos56_64.box'

  # Forward a port from the guest to the host, which allows for outside
  # computers to access the VM, whereas host only networking does not.
  config.vm.forward_port 80, 8080

  # Share an additional folder to the guest VM. The first argument is
  # an identifier, the second is the path on the guest to mount the
  # folder, and the third is the path on the host to the actual folder.
  # config.vm.share_folder "v-data", "/vagrant_data", "../data"

  config.vm.provision :puppet do |puppet|
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
