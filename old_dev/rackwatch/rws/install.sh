#!/bin/bash

function update_apt_get
{
  echo "update_apt_get"

  grep -q "deb ${apache2_2_pkg_url} dapper main" /etc/apt/sources.list
  (( ${?} != 0 )) && echo "deb ${apache2_2_pkg_url} dapper main" >>/etc/apt/sources.list

  grep -q "Acquire::http::Proxy \"${http_proxy}\";" /etc/apt/apt.conf
  (( ${?} != 0 )) &&  echo "Acquire::http::Proxy \"${http_proxy}\";" >>/etc/apt/apt.conf

  export http_proxy=${http_proxy}

  wget --proxy ${apache2_2_pkg_url}/${apache2_2_pkg_asc} && \
  sudo apt-key add ${apache2_2_pkg_asc} && \
  sudo apt-get update

  return ${?}
}

function install_apt_pkgs
{
  echo "install_apt_pkgs"

  sudo apt-get -y install ${apt_pkg_list}

  return ${?}
}

function install_ruby
{
  echo "install_ruby"

  cd src
  pkg_ruby_dir=$(strip_ext ${pkg_ruby})

  [[ -d ${pkg_ruby_dir} ]] && sudo rm -rf ${pkg_ruby_dir}

  [[ ! -f ${pkg_ruby} ]] && { echo "Cannot find ${pkg_ruby}"; return 1; }
  tar zxf ${pkg_ruby}

  cd ${pkg_ruby_dir}
  sudo ./configure && \
  sudo make && \
  sudo make install
  rc=${?}
  cd ${my_pwd}
  return ${rc}
}

function install_rubygems
{
  echo "install_rubygems"

  cd src
  pkg_rubygems_dir=$(strip_ext ${pkg_rubygems})

  [[ -d ${pkg_rubygems_dir} ]] && sudo rm -rf ${pkg_rubygems_dir}

  [[ ! -f ${pkg_rubygems} ]] && { echo "Cannot find ${pkg_rubygems}"; return 1; }
  tar zxf ${pkg_rubygems}

  cd ${pkg_rubygems_dir}
  sudo ruby setup.rb
  rc=${?}
  cd ${my_pwd}
  return ${rc}
}

function install_soap4r
{
  echo "install_soap4r"

  cd src
  pkg_soap4r_dir=$(strip_ext ${pkg_soap4r})

  [[ -d ${pkg_soap4r_dir} ]] && sudo rm -rf ${pkg_soap4r_dir}

  [[ ! -f ${pkg_soap4r} ]] && { echo "Cannot find ${pkg_soap4r}"; return 1; }
  tar zxf ${pkg_soap4r}

  cd ${pkg_soap4r_dir}
  sudo ruby install.rb
  rc=${?}
  cd ${my_pwd}
  return ${rc}
}

function install_rexml
{
  echo "install_rexml"

  cd src
  pkg_rexml_dir=$(strip_ext ${pkg_rexml})

  [[ -d ${pkg_rexml_dir} ]] && sudo rm -rf ${pkg_rexml_dir}

  [[ ! -f ${pkg_rexml} ]] && { echo "Cannot find ${pkg_rexml}"; return 1; }
  tar zxf ${pkg_rexml}

  cd ${pkg_rexml_dir}/bin
  sudo ruby install.rb
  rc=${?}
  cd ${my_pwd}
  return ${rc}
}

function install_ruby_ldap_support
{
  echo "install_ruby_ldap"

  cd src
  pkg_ruby_ldap_dir=$(strip_ext ${pkg_ruby_ldap})

  [[ -d ${pkg_ruby_ldap_dir} ]] && sudo rm -rf ${pkg_ruby_ldap_dir}

  [[ ! -f ${pkg_ruby_ldap} ]] && { echo "Cannot find ${pkg_ruby_ldap}"; return 1; }
  tar zxf ${pkg_ruby_ldap}

  cd ${pkg_ruby_ldap_dir}
  ruby extconf.rb --with-openldap2 && \
  sudo make && \
  sudo make install
  rc=${?}
  cd ${my_pwd}
  return ${rc}
} 

function strip_ext
{
  echo ${1}|sed "s/${pkg_ext_list}//"
}

function install_ruby_extensions
{
  pkg_ruby_dir=$(strip_ext ${pkg_ruby})

  for ext in ${ruby_extension_list}
  do
    ext_dir=src/${pkg_ruby_dir}/ext/${ext}
    rc=0

    echo "Install ${ext} extension"

    [[ ! -d ${ext_dir} ]] && { echo "Cannot find ${ext_dir}"; return 1; }

    cd ${ext_dir}
    sudo ruby extconf.rb && \
    sudo make clean && \
    sudo make && \
    sudo make install
    rc=${?}
    cd ${my_pwd}
    if (( ${rc} != 0 ))
    then
      echo "An error occurred installing ${ext}"
      break
    fi
  done

  return ${rc}
}

function install_gem_pkgs
{
  echo "install_gem_pkgs"

  sudo gem install -p ${http_proxy} ${gem_pkg_list}
  return ${?}
}

function setup_symlinks
{
  echo "setup_symlinks"

  [[ ! -f ${file_mongrel_rails} ]] && { echo "Could not find ${file_mongrel_rails}"; return 1; }

  if [[ ! -h ${symlink_mongrel_rails} ]]
  then
    sudo ln -s ${file_mongrel_rails} ${symlink_mongrel_rails}
  fi
}

function enable_apache_mods
{
    echo "enable_apache_mods"

    for mod in ${apache_mods_list}
    do
        sudo a2enmod ${mod}
    done
    return ${?}
}

script_name=${0##*/}
log=${script_name}.log

my_pwd=$(pwd)

# Main

[[ ! -f ./install.cfg ]] && { echo "Cannot find ./install.cfg, exiting install."; exit 1; }

source ./install.cfg

echo "Now installing Ruby libraries..."

[[ -f ${log} ]] && mv ${log} ${log}.old

update_apt_get >> ${log} 2>&1 && \
install_apt_pkgs >> ${log} 2>&1 && \
install_ruby >> ${log} 2>&1 && \
install_ruby_extensions >> ${log} 2>&1 && \
install_rubygems >> ${log} 2>&1 && \
install_gem_pkgs >> ${log} 2>&1 && \
install_soap4r >> ${log} 2>&1 && \
install_rexml >> ${log} 2>&1 && \
install_ruby_ldap_support >> ${log} 2>&1 && \
setup_symlinks >> ${log} 2>&1 && \
enable_apache_mods >> ${log} 2>&1

if [[ ${?} == 0 ]]
then
  echo "Successful install"
else
  echo "Install failed; check the log for more information."
fi
