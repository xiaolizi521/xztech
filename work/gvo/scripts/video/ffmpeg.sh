#!/bin/sh

# This script will automate the installation of the video libraries on a new serve.r

MAIN = "/usr/local/src"

# Enter the new src director

mkdir /usr/local/src

cd /usr/local/src

# install dependencies
yum -y install gcc gmake make libcpp libgcc libstdc++ gcc4 gcc4-c++ gcc4-gfortran subversion patch zlib-devel


wget http://kernel.org/pub/software/scm/git/git-1.6.5.5.tar.gz

tar xvzf git-1.6.5.5.tar.gz
cd git-1.6.5.5
./configure && make && make install
cd $MAIN

# Lets instal MPlayer's essential codecs precompiled
wget http://www3.mplayerhq.hu/MPlayer/releases/codecs/essential-20071007.tar.bz2
tar jxvf essential-20071007.tar.bz2
mkdir /usr/local/lib/codecs/
mv /usr/local/src/essential-20071007/* /usr/local/lib/codecs/
chmod -R 755 /usr/local/lib/codecs/
mkdir /usr/local/src/tmp
chmod 777 /usr/local/src/tmp
export TMPDIR=/usr/local/src/tmp
export PKG_CONFIG_PATH=$PKG_CONFIG_PATH:/usr/local/lib/pkgconfig
cd $MAIN


# Now, lets get some libs.
# download the various libs needed

mkdir /usr/local/src/libs
cd /usr/local/src/libs

# GPac
wget http://downloads.sourceforge.net/gpac/gpac-0.4.5.tar.gz
tar xvzf gpac-0.4.5.tar.gz
cd gpac-0.4.5
./configure && make && make install
cd $MAIN

# Vorbis, FLAC, Ogg and Theora
wget http://downloads.xiph.org/releases/vorbis/libvorbis-1.2.3.tar.gz
tar xvzf libvorbis-1.2.3.tar.gz
cd libvorbis-1.2.3
./configure && make && make install
cd $MAIN/libs

wget http://downloads.xiph.org/releases/flac/flac-1.2.1.tar.gz
tar xvzf flac-1.2.1.tar.gz
cd flac-1.2.1
./configure && make && make install
cd $MAIN/libs

wget http://downloads.xiph.org/releases/theora/libtheora-1.1.1.tar.bz2
tar xvjf libtheora-1.1.1.tar.bz2
cd libtheora-1.1.1
./configure && make && make install
cd $MAIN/libs

wget http://downloads.xiph.org/releases/ogg/libogg-1.1.4.tar.gz
tar xvzf libogg-1.1.4.tar.gz
cd libogg-1.1.4
./configure && make && make install
cd $MAIN/libs

# Lame MP3 Encoder
wget http://downloads.sourceforge.net/project/lame/lame/3.98.2/lame-398-2.tar.gz?use_mirror=voxel
tar xvzf lame-398-2.tar.gz
cd lame-398-2
./configure && make && make install
cd $MAIN/libs

# FAAD 2 & FAAC
wget http://downloads.sourceforge.net/project/faac/faad2-src/faad2-2.7/faad2-2.7.tar.gz?use_mirror=iweb
tar xvzf faad2-2.7.tar.gz
cd faad2-2.7
./configure && make && make install
cd $MAIN/libs

# LibDirac, LIBOIL and schroedinger
wget http://downloads.sourceforge.net/project/dirac/dirac-codec/Dirac-1.0.2/dirac-1.0.2.tar.gz?use_mirror=iweb
wget http://downloads.sourceforge.net/project/schrodinger/schrodinger/1.0.0/schroedinger-1.0.0.tar.gz?use_mirror=cdnetworks-us-1
wget http://liboil.freedesktop.org/download/liboil-0.3.16.tar.gz

tar xvzf dirac-1.02.tar.gz
tar xvzf schroedinger-1.0.0.tar.gz
tar xvzf liboil-0.3.16.tar.gz

cd liboil-0.3.16
./configure && make && make install
cd $MAIN/libs

cd dirac-1.02
./configure && make && make install all
cd $MAIN/libs

cd schroedinger-1.0.0
./configure && make && make install
cd $MAIN/libs


# Ruby & FLV Tool
wget ftp://ftp.ruby-lang.org/pub/ruby/1.9/ruby-1.9.1-p376.tar.gz
wget http://rubyforge.org/frs/download.php/17497/flvtool2-1.0.6.tgz

tar xvzf ruby-1.9.1-p376.tar.gz
cd ruby-1.9.1-p376
./configure && make && make install
cd $MAIN/libs

tar xvzf flvtool2-1.0.6.tgz
cd flvtool2-1.0.6.tgz
ruby setup.rb config
ruby setup.rb install
cd $MAIN/libs

#opencore & speex

wget http://downloads.sourceforge.net/project/opencore-amr/opencore-amr/0.1.2/opencore-amr-0.1.2.tar.gz?use_mirror=softlayer
wget http://downloads.xiph.org/releases/speex/speex-1.2rc1.tar.gz

tar xvzf opencore-amr-0.1.2.tar.gz
cd opencore-amr-0.1.2
./configure && make && make install
cd $MAIN

tar xvzf speex-1.2rc1.tar.gz
cd speex-1.2rc1.tar.gz
./configure && make && make install
cd $MAIN

# xvidcore

wget http://downloads.xvid.org/downloads/xvidcore-1.2.2.tar.gz
tar xvzf xvidcore-1.2.2.tar.gz
cd xvidcore-1.2.2
./configure && make && make install
cd $MAIN

# Media Info RPMS
wget http://sourceforge.net/projects/zenlib/files/ZenLib/0.4.9/libzen0-devel-0.4.9-1.i386.RHEL_5.rpm/download
wget http://downloads.sourceforge.net/mediainfo/libmediainfo0-devel-0.7.25-1.i386.CentOS_5.rpm
wget http://downloads.sourceforge.net/mediainfo/mediainfo-0.7.25-1.i386.CentOS_5.rpm
wget http://downloads.sourceforge.net/zenlib/libzen0-0.4.9-1.i386.CentOS_5.rpm
wget http://downloads.sourceforge.net/mediainfo/libmediainfo0-0.7.25-1.i386.CentOS_5.rpm

rpm -ivh *.rpm

# x264 & YASM
wget http://www.tortall.net/projects/yasm/releases/yasm-0.8.0.tar.gz
tar xvzf yasm-0.8.0.tar.gz
cd yasm-0.8.0
./configure && make && make install
cd $MAIN

git clone git://git.videolan.org/x264.git
cd x264
./configure && make && make install
cd $MAIN

# Now, lets get FFMpeg and MPlayer (MEncoder)

svn checkout svn://svn.mplayerhq.hu/ffmpeg/trunk ffmpeg
svn checkout svn://svn.mplayerhq.hu/mplayer/trunk mplayer

cd mplayer
./configure && make && make install
cd $MAIN

cd ffmpeg
svn update
./configure --enable-shared --enable-gpl --enable-nonfree --enable-postproc --enable-bzlib --enable-libopencore-amrnb --enable-libopencore-amrwb --enable-libdirac --enable-libfaac --enable-libfaad --enable-libgsm --enable-libmp3lame --enable-libnut --enable-libspeex --enable-libtheora --enable-libvorbis --enable-libx264 --enable-libxvid --enable-zlib --enable-version3 --enable-pthreads
make
make install
cd $MAIN

echo "/usr/local/lib" >> /etc/ld.so.conf
ldconfig

ffmpeg

# If all went well, ffmpeg is now installed properly!
