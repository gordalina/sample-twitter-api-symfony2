Vagrant::Config.run do |config|

  # get a box here http://www.vagrantbox.es/ | precise32 and precise64 working well out of the box
  # uncomment line below and replace 'base' with the name of the vm of your choice
  config.vm.box = "base"
  config.vm.network :hostonly, "10.10.10.10"

  # set the memory - 1GB is good for SC
  config.vm.customize ["modifyvm", :id, "--memory", 1024]

  # remove the next line when running on a windows host system (Windows does not have NFS support)
  config.vm.share_folder("v-root", "/vagrant", ".", :nfs => true)
  config.vm.provision :puppet do |puppet|
    puppet.manifests_path = ".puppet/vagrant/manifests"
    puppet.manifest_file = "app.pp"
  end

end
