import { __ } from "@wordpress/i18n";
import { registerPlugin } from "@wordpress/plugins";
import { PluginDocumentSettingPanel } from "@wordpress/editor";
import { useSelect } from "@wordpress/data";
import { useEntityProp } from "@wordpress/core-data";
import { useState, useEffect } from "@wordpress/element";
import {
	ToggleControl,
	DateTimePicker,
	TextControl,
	SelectControl,
	PanelRow,
	ComboboxControl,
	Spinner,
} from "@wordpress/components";
import apiFetch from "@wordpress/api-fetch";
import { addQueryArgs } from "@wordpress/url";

/**
 * Custom hook to search posts and pages.
 */
const usePostSearch = (selectedPostId) => {
	const [searchResults, setSearchResults] = useState([]);
	const [isLoading, setIsLoading] = useState(false);
	const [selectedPost, setSelectedPost] = useState(null);

	// Fetch the selected post details on mount.
	useEffect(() => {
		if (!selectedPostId) {
			setSelectedPost(null);
			return;
		}

		const fetchSelectedPost = async () => {
			try {
				// Try fetching as a page first.
				const page = await apiFetch({
					path: addQueryArgs(`/wp/v2/pages/${selectedPostId}`, {
						_fields: "id,title",
					}),
				});
				setSelectedPost({
					value: page.id,
					label: page.title.rendered || __("(No title)", "jcore-portti"),
				});
			} catch {
				try {
					// If not a page, try as a post.
					const post = await apiFetch({
						path: addQueryArgs(`/wp/v2/posts/${selectedPostId}`, {
							_fields: "id,title",
						}),
					});
					setSelectedPost({
						value: post.id,
						label: post.title.rendered || __("(No title)", "jcore-portti"),
					});
				} catch {
					setSelectedPost(null);
				}
			}
		};

		fetchSelectedPost();
	}, [selectedPostId]);

	const searchPosts = async (searchTerm) => {
		if (!searchTerm || searchTerm.length < 2) {
			setSearchResults([]);
			return;
		}

		setIsLoading(true);

		try {
			const [pages, posts] = await Promise.all([
				apiFetch({
					path: addQueryArgs("/wp/v2/pages", {
						search: searchTerm,
						per_page: 10,
						_fields: "id,title,type",
					}),
				}),
				apiFetch({
					path: addQueryArgs("/wp/v2/posts", {
						search: searchTerm,
						per_page: 10,
						_fields: "id,title,type",
					}),
				}),
			]);

			const results = [
				...pages.map((page) => ({
					value: page.id,
					label: `${page.title.rendered || __("(No title)", "jcore-portti")} (${__("Page", "jcore-portti")})`,
				})),
				...posts.map((post) => ({
					value: post.id,
					label: `${post.title.rendered || __("(No title)", "jcore-portti")} (${__("Post", "jcore-portti")})`,
				})),
			];

			setSearchResults(results);
		} catch (error) {
			// eslint-disable-next-line no-console
			console.error("Error searching posts:", error);
			setSearchResults([]);
		} finally {
			setIsLoading(false);
		}
	};

	return {
		searchResults,
		isLoading,
		searchPosts,
		selectedPost,
	};
};

const CampaignContentSettings = () => {
	const postType = useSelect((select) =>
		select("core/editor").getCurrentPostType(),
	);

	const [meta = {}, setMeta] = useEntityProp("postType", postType, "meta");

	const selectedPostId = meta._jcore_portti_selected_post || 0;
	const { searchResults, isLoading, searchPosts, selectedPost } =
		usePostSearch(selectedPostId);

	const updateMeta = (key, value) => {
		setMeta({ ...(meta || {}), [key]: value });
	};

	if (postType !== "jcore-portal-content" || !meta) {
		return null;
	}

	const hasSelectedPost = selectedPostId > 0;

	return (
		<PluginDocumentSettingPanel
			name="jcore-portti-settings"
			title={__("Campaign Settings", "jcore-portti")}
		>
			<div className="jcore-portti-meta-fields">
				<PanelRow>
					<div style={{ marginBottom: "20px", width: "100%" }}>
						<label
							style={{
								display: "block",
								marginBottom: "8px",
								fontWeight: "600",
							}}
						>
							{__("Start Date", "jcore-portti")}
						</label>
						<ToggleControl
							label={__("Set Start Date", "jcore-portti")}
							checked={!!meta._jcore_portti_start_date}
							onChange={(isEnabled) =>
								updateMeta(
									"_jcore_portti_start_date",
									isEnabled ? new Date().toISOString() : "",
								)
							}
						/>
						{meta._jcore_portti_start_date ? (
							<DateTimePicker
								currentDate={meta._jcore_portti_start_date}
								onChange={(date) =>
									updateMeta("_jcore_portti_start_date", date)
								}
								is12Hour={false}
							/>
						) : null}
					</div>
				</PanelRow>

				<PanelRow>
					<div style={{ marginBottom: "20px", width: "100%" }}>
						<label
							style={{
								display: "block",
								marginBottom: "8px",
								fontWeight: "600",
							}}
						>
							{__("End Date", "jcore-portti")}
						</label>
						<ToggleControl
							label={__("Set End Date", "jcore-portti")}
							checked={!!meta._jcore_portti_end_date}
							onChange={(isEnabled) =>
								updateMeta(
									"_jcore_portti_end_date",
									isEnabled ? new Date().toISOString() : "",
								)
							}
						/>
						{meta._jcore_portti_end_date ? (
							<DateTimePicker
								currentDate={meta._jcore_portti_end_date}
								onChange={(date) => updateMeta("_jcore_portti_end_date", date)}
								is12Hour={false}
							/>
						) : null}
					</div>
				</PanelRow>

				<PanelRow>
					<div style={{ marginBottom: "20px", width: "100%" }}>
						<div style={{ position: "relative" }}>
							<ComboboxControl
								label={__("Selected Page/Post", "jcore-portti")}
								value={selectedPostId || null}
								options={
									selectedPost && !searchResults.length
										? [selectedPost]
										: searchResults
								}
								onChange={(value) =>
									updateMeta("_jcore_portti_selected_post", value || 0)
								}
								onFilterValueChange={(inputValue) => searchPosts(inputValue)}
								help={__(
									"Search for a specific page or post to show this content on.",
									"jcore-portti",
								)}
							/>
							{isLoading && (
								<div
									style={{ position: "absolute", right: "8px", top: "32px" }}
								>
									<Spinner />
								</div>
							)}
						</div>
					</div>
				</PanelRow>

				{!hasSelectedPost && (
					<TextControl
						label={__("Route Path", "jcore-portti")}
						value={meta._jcore_portti_route_path}
						onChange={(value) => updateMeta("_jcore_portti_route_path", value)}
						help={__(
							"Example: /products/* or /shop. Leave empty for all pages.",
							"jcore-portti",
						)}
					/>
				)}

				<SelectControl
					label={__("Priority", "jcore-portti")}
					value={meta._jcore_portti_priority}
					options={[
						{ label: __("High", "jcore-portti"), value: "high" },
						{
							label: __("Medium", "jcore-portti"),
							value: "medium",
						},
						{ label: __("Low", "jcore-portti"), value: "low" },
					]}
					onChange={(value) => updateMeta("_jcore_portti_priority", value)}
				/>
			</div>
		</PluginDocumentSettingPanel>
	);
};

registerPlugin("jcore-portti-sidebar", {
	render: CampaignContentSettings,
});
