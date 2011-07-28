# Makefile
# 	 Makefile for the pl/perlu triggers that restrict certain updates 
# 	 to the target table
#
subdir = contrib/check_updates

out_in := $(wildcard expected/*.out.in)
out := $(out_in:.out.in=.out)

DATA = check_helper.pm
DATA_built = deny_updates.sql allow_on_condition.sql check_updates.sql  $(out)
REGRESS = deny_updates_test allow_on_condition_test check_updates_test

ifdef USE_PGXS
PGXS := $(shell pg_config --pgxs)
include $(PGXS)
else
top_builddir = ../..
include $(top_builddir)/src/Makefile.global
include $(top_srcdir)/contrib/contrib-global.mk
endif

all:
	@echo "Run make install to install the trigger functions"

# Hack: fill perl module path in sql and expected/.out files

%.sql: %.sql.in
	@sed 's,PG_CONTRIB_DIR,$(shell pg_config --sharedir)/contrib,g' <$< >$@

$(out): 
%.out: %.out.in
	@sed 's,PG_CONTRIB_DIR,$(shell pg_config --sharedir)/contrib,g' <$< >$@

