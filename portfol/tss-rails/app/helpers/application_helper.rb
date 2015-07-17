module ApplicationHelper
	def set_title(page_title)
  		content_for(:title) {
  			page_title
  		}
	end
	def set_body_class(page_body)
		content_for(:body_class) do
			page_body
		end
	end
end
