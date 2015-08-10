Vagrant::Config.run do |config|

  bibconfig = Bib::Vagrant::Config.new
  vagrantconfig = bibconfig.get

  config.vm.boot_mode = vagrantconfig['gui']
  config.vm.box = 'https://s3.amazonaws.com/easybibdeployment/easybib-ubuntu-14.04.1_vbox-4.3.12_chef-11.10.4_0.1.box'

  config.vm.network :hostonly, '10.2.0.2'

    config.vm.customize [
      "modifyvm", :id,
      "--name", 'opcache-primer'
    ]

  config.vm.provision :shell, :inline => "sudo apt-spy2 fix --launchpad --country=de --commit"
  config.vm.provision :shell, :inline => "sudo apt-get update -y"

  config.vm.provision :chef_solo do |chef|
    chef.cookbooks_path = vagrantconfig['cookbook_path']
    chef.add_recipe "easybib::role-phpapp"
    chef.log_level = vagrantconfig['chef_log_level']
  end
end
