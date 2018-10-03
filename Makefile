.DEFAULT_GOAL := help

.PHONY: help
help: ## Dislay this help
	@IFS=$$'\n'; for line in `grep -E '^[a-zA-Z_#-]+:?.*?## .*$$' $(MAKEFILE_LIST)`; do if [ "$${line:0:2}" = "##" ]; then \
	echo $$line | awk 'BEGIN {FS = "## "}; {printf "\n\033[33m%s\033[0m\n", $$2}'; else \
	echo $$line | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-25s\033[0m %s\n", $$1, $$2}'; fi; \
	done; unset IFS;
modman: ## Regenerate modman
	vendor/bin/generate-modman --include-others  --include-others-files
