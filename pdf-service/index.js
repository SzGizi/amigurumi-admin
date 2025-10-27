import express from "express";
import bodyParser from "body-parser";
import fs from "fs";
import path from "path";
import puppeteer from "puppeteer";

const app = express();
app.use(bodyParser.json({ limit: "10mb" }));

const PORT = 3000;

function loadTemplate(templateName, htmlContent, title = "Amigurumi Pattern") {
  const templateDir = path.resolve(`./templates/${templateName}`);
  const layout = fs.readFileSync(path.join(templateDir, "layout.html"), "utf8");
  const css = fs.readFileSync(path.join(templateDir, "style.css"), "utf8");

  return layout
    .replace("{{CONTENT}}", htmlContent)
    .replace("{{STYLE}}", `<style>${css}</style>`)
    .replace("{{TITLE}}", title);
}

app.post("/generate", async (req, res) => {
  const { html, options } = req.body;
  const template = options?.template || "classic";
  const format = options?.format || "A4";
  const type = options?.type || "pdf"; // "pdf" or "png"
  const title = options?.title || "Amigurumi Pattern";

  try {
    const browser = await puppeteer.launch({
      headless: "new",
      args: ["--no-sandbox", "--disable-setuid-sandbox"],
    });
    const page = await browser.newPage();

    const finalHtml = loadTemplate(template, html, title);
    await page.setContent(finalHtml, { waitUntil: "networkidle0" });

    let outputBuffer;
    if (type === "pdf") {
      outputBuffer = await page.pdf({
        format,
        printBackground: true,
        margin: { top: "10mm", bottom: "10mm" },
      });
      res.contentType("application/pdf");
    } else {
      const clip =
        format === "1:1" ? { width: 1080, height: 1080, x: 0, y: 0 } : undefined;
      outputBuffer = await page.screenshot({ fullPage: !clip, clip });
      res.contentType("image/png");
    }

    await browser.close();
    res.send(outputBuffer);
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: "PDF generation failed", details: error.message });
  }
});

app.listen(PORT, () => {
  console.log(`✅ PDF Service running on port ${PORT}`);
});
